<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Perfomance;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Structure;

class PerformanceController extends Controller
{

    public function index()
{
    
    $pentry = Perfomance::distinct()->get(['ID', 'admno']);

    return view('students.pentry', compact('pentry'));
}

 public function reports()
{
    
    $preports = Perfomance::distinct()->get(['ID', 'admno']);

    return view('students.preports', compact('preports'));
}
    // Fetch students for a subject+class+exam
  public function getStudents(Request $request)
{
    $request->validate([
        'subject_id' => 'required|integer',
        'class_id'   => 'required|integer',
        'exam_id'    => 'nullable|string'
    ]);

    // ✅ Get active period
    $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->value('ID');
    if (!$activePeriod) {
        return response()->json([
            'errors' => ['general' => ['No active period found']]
        ], 422);
    }

    $subjectId = $request->subject_id;
    $classId   = $request->class_id;

    // ✅ Check subject assignment (try classid first, fallback to classident)
    $assigned = DB::table('tblsubassign')
        ->where('subid', $subjectId)
        ->where(function ($q) use ($classId) {
            $q->where('classid', $classId)
              ->orWhere('classident', $classId);
        })
        ->exists();

    if (!$assigned) {
        return response()->json([
            'students' => [],
            'message'  => 'Subject not assigned to this class'
        ], 200);
    }

    // ✅ Get students (check both stream and class)
   $students = DB::table('students as s')
    ->where(function ($q) use ($classId) {
        $q->where('s.stream', $classId)
          ->orWhere(function ($sub) use ($classId) {
              // When matching by classid fallback (claid), ensure student was assigned
              $sub->where('s.claid', $classId)
                  ->whereExists(function ($exists) use ($classId) {
                      $exists->select(DB::raw(1))
                             ->from('tblsubassign')
                             ->whereRaw('tblsubassign.stadmno = s.admno')
                             ->whereRaw('(tblsubassign.classid = ? OR tblsubassign.classident = ?)', [$classId, $classId]);
                  });
          });
    })
    ->select('s.admno', 's.sirname', 's.othername', 's.stream', 's.claid')
    ->get();


    // ✅ No students found
    if ($students->isEmpty()) {
        return response()->json([
            'students' => [],
            'message'  => 'No students found in this class or stream'
        ], 200);
    }

    // ✅ If an exam is selected, attach marks with fallback (classid → classident)
    if ($request->filled('exam_id')) {
        $existing = Perfomance::where('subid', $subjectId)
            ->where(function ($q) use ($classId) {
                $q->where('classid', $classId)
                 ->orWhere('classident', $classId);
            })
            ->where('examtype', $request->exam_id)
            ->where('examperiod', $activePeriod)
            ->pluck('marks', 'admno');

        $students = $students->map(function ($stu) use ($existing) {
            $stu->marks = $existing[$stu->admno] ?? null;
            return $stu;
        });
    } else {
        $students = $students->map(function ($stu) {
            $stu->marks = null;
            return $stu;
        });
    }

    return response()->json([
        'students' => $students,
        'message'  => 'Students loaded successfully'
    ]);
}





    // Save marks
public function saveMarks(Request $request)
{
    Log::debug('Request data:', $request->all());

    $request->validate([
        'subject_id' => 'required|string',
        'class_id'   => 'required|string',
        'exam_id'    => 'required|string', // exam type e.g. CAT1, CAT2, Endterm
        'marks'      => 'array|required',
        'mstatus'    => 'required|in:draft,final'
    ]);

    $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->value('ID');
    if (!$activePeriod) {
        return response()->json([
            'errors' => ['general' => ['No active period found']]
        ], 422);
    }

    DB::beginTransaction();

    try {
        $classId = $request->class_id;

        // 🔹 Try to find assigned teacher — fallback between classid and classident
        $teacher = DB::table('tblsubassign')
            ->where('subid', $request->subject_id)
            ->where(function ($q) use ($classId) {
                $q->where('classid', $classId)
                  ->orWhere('classident', $classId);
            })
            ->select('classtr', 'classid', 'classident')
            ->first();

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'No teacher assigned for this subject and class'
            ], 422);
        }

        // ✅ Determine which column matched (classid or classident)
        $useClassIdent = ($teacher->classident == $classId);
        $teacherId = $teacher->classtr;

        // 🔹 Determine examcount for this examtype + period
        $existingExamCount = Perfomance::where('examtype', $request->exam_id)
            ->where('examperiod', $activePeriod)
            ->value('examcount');

        $newExamCount = $existingExamCount ?: (Perfomance::max('examcount') + 1 ?? 1);

        foreach ($request->marks as $row) {
            if (!isset($row['admno'])) continue;

            // 🔹 Fallback between classid/classident for existing check
            $existing = Perfomance::where('admno', $row['admno'])
                ->where('examtype', $request->exam_id)
                ->where('subid', $request->subject_id)
                ->where(function ($q) use ($classId) {
                    $q->where('classid', $classId)
                      ->orWhere('classident', $classId);
                })
                ->where('examperiod', $activePeriod)
                ->first();

            if ($existing && $existing->mstatus === 'final') {
                continue; // skip finalized marks
            }

            // 🔹 Dynamic class column based on match
            $classColumn = $useClassIdent ? 'classident' : 'classid';

            Perfomance::updateOrCreate(
                [
                    'admno'      => $row['admno'],
                    'examtype'   => $request->exam_id,
                    'subid'      => $request->subject_id,
                    'examperiod' => $activePeriod,
                    $classColumn => $classId,
                ],
                [
                    'marks'      => $row['marks'],
                    'clateach'   => $teacherId,
                    'mstatus'    => $request->mstatus,
                    'examcount'  => $existing?->examcount ?? $newExamCount,
                ]
            );
        }

        DB::commit();
        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error saving marks: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}





public function previewStudentReport(Request $request) {
    try {
        // Validation
        $validator = Validator::make($request->all(), [
            'admno' => 'required|string',
            'examtype' => 'nullable|string'//, // Allow filtering by exam type
            //'examperiod' => 'nullable|string' // Allow filtering by exam period
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Get school information
        $school = Structure::first();
        
        if (!$school) {
            return response()->json([
                'error' => 'School information not found'
            ], 422);
        }

        // Get active period
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->first();
        
        if (!$activePeriod) {
            return response()->json([
                'error' => 'No active academic period found'
            ], 422);
        }

        // Get student information with better field handling
        $student = DB::table('students as s')
            ->leftJoin('tblclasses as c', 's.claid', '=', 'c.ID')
            ->leftJoin('streams as st', 'c.stid', '=', 'st.ID')
            ->select([
                's.admno',
                's.sirname',
                's.othername',
                'c.claname',
                'st.strmname',
                's.admdate',
                's.claid as class_id'
            ])
            ->where('s.admno', $request->admno)
            ->first();

        if (!$student) {
            return response()->json([
                'error' => 'Student not found'
            ], 404);
        }

        // Build performance query with proper joins
        $performanceQuery = DB::table('performancetbl as p')
            ->leftJoin('tblsubjects as sub', 'p.subid', '=', 'sub.ID')
            ->leftJoin('tblteachers as t', 'p.clateach', '=', 't.ID')
            //->leftJoin('tblexams as e', 'p.examtype', '=', 'e.ID')
            ->select([
                'p.admno',
                'p.examtype',
                'p.examperiod',
                'p.marks',
                'p.mstatus',
                'p.subid',
                'sub.sname as subject_name',
                'sub.scode as subject_code',
                DB::raw('CONCAT(COALESCE(t.fname, ""), " ", COALESCE(t.surname, "")) as teacher_name'),
                'p.created_at'
            ])
            ->where('p.admno', $request->admno)
            ->where('p.examtype',  $request->examtype)
            ->where('p.mstatus', 'final'); // Only show finalized marks

        // Apply optional filters
        if ($request->filled('examtype')) {
            $performanceQuery->where('p.examtype', $request->examtype);
        }

        $performance = $performanceQuery
            ->orderBy('sub.sname', 'asc')
            ->get();

        if ($performance->isEmpty()) {
            return response()->json([
                'error' => 'No finalized performance records found for this student'
            ], 404);
        }

        // Calculate statistics
        $totalMarks = $performance->sum('marks');
        $subjectsCount = $performance->count();
        $averageMarks = $subjectsCount > 0 ? round($totalMarks / $subjectsCount, 2) : 0;
        
        // Calculate grades and additional statistics
        $performanceWithGrades = $performance->map(function ($record) {
            $record->grade = $this->calculateGrade($record->marks);
            $record->points = $this->calculatePoints($record->marks);
            return $record;
        });

        // Group by exam type if multiple exams
        $performanceByExam = $performanceWithGrades->groupBy('examtype');

        // Add subject positions
$performanceWithPositions = $performanceWithGrades->map(function ($record) use ($student, $request) {
    // Get ranks for this subject in the student's class
    $subjectRanks = DB::table('performancetbl as p')
        ->leftJoin('students as s', 'p.admno', '=', 's.admno')
        ->where('s.claid', $student->class_id)
        ->where('p.examtype', $request->examtype)
        ->where('p.subid', $record->subid)   // same subject
        ->where('p.mstatus', 'final')
        ->orderByDesc('p.marks')
        ->pluck('p.admno')
        ->toArray();

    $record->subject_position = array_search($student->admno, $subjectRanks) + 1;
    $record->subject_out_of = count($subjectRanks);

    return $record;
});

// Get overall ranks for the exam
$classRanks = DB::table('performancetbl as p')
    ->leftJoin('students as s', 'p.admno', '=', 's.admno')
    ->where('s.claid', $student->class_id)
    ->where('p.examtype', $request->examtype)
    ->where('p.mstatus', 'final')
    ->select('p.admno', DB::raw('SUM(p.marks) as total_marks'))
    ->groupBy('p.admno')
    ->orderByDesc('total_marks')
    ->pluck('admno')
    ->toArray();

$studentOverallPosition = array_search($student->admno, $classRanks) + 1;
$totalStudents = count($classRanks);



        // Get class statistics for comparison (optional)
        $classStats = $this->getClassStatistics($student->class_id,  $request->examtype);

        $data = [
            'school' => $school,
            'student' => $student,
            'performance' => $performanceWithPositions,
            'performanceByExam' => $performanceByExam,
            'totalMarks' => $totalMarks,
            'averageMarks' => $averageMarks,
            'subjectsCount' => $subjectsCount,
            'period' => $activePeriod,
            'classStats' => $classStats,
            'overallPosition' => $studentOverallPosition,
'overallOutOf' => $totalStudents,
            'generatedDate' => now(),
            'studentFullName' => trim(($student->sirname ?? '') . ' ' . ($student->othername ?? ''))
        ];

        // Generate PDF
        $pdf = Pdf::loadView('reports.student_reportform', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="student_report_' . $request->admno . '.pdf"');

    } catch (\Exception $e) {
        Log::error('Error generating student report: ' . $e->getMessage(), [
            'admno' => $request->admno ?? 'N/A',
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => 'Failed to generate student report. Please try again.',
            'details' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

/*

private function calculateGrade($marks) {
    if ($marks >= 80) return 'A';
    if ($marks >= 70) return 'B';
    if ($marks >= 60) return 'C';
    if ($marks >= 50) return 'D';
    if ($marks >= 40) return 'E';
    return 'F';
} 
    */

/**
 * Calculate points based on marks
 */
private function calculatePoints($marks) {
    if ($marks >= 80) return 12;
    if ($marks >= 75) return 11;
    if ($marks >= 70) return 10;
    if ($marks >= 65) return 9;
    if ($marks >= 60) return 8;
    if ($marks >= 55) return 7;
    if ($marks >= 50) return 6;
    if ($marks >= 45) return 5;
    if ($marks >= 40) return 4;
    if ($marks >= 35) return 3;
    if ($marks >= 30) return 2;
    return 1;
}

/**
 * Get class statistics for comparison
 */
private function getClassStatistics($classId, $periodId) {
    return DB::table('performancetbl as p')
        ->leftJoin('students as s', 'p.admno', '=', 's.admno')
        ->where('s.claid', $classId)
        ->where('p.examtype', $periodId)
        ->where('p.mstatus', 'final')
        ->selectRaw('
            COUNT(DISTINCT p.admno) as total_students,
            AVG(p.marks) as class_average,
            MAX(p.marks) as highest_mark,
            MIN(p.marks) as lowest_mark
        ')
        ->first();
}

public function getperiods()
{
    $periods = Perfomance::join('tblperiods', 'performancetbl.examperiod', '=', 'tblperiods.ID')
        ->select('performancetbl.examperiod', 'tblperiods.periodname')
        ->distinct()
        ->get();

    return response()->json([
        'data' => $periods,
    ]);
}
public function getpstudents(Request $request)
{ 
    try {
        $examperiod = $request->input('selectedperiod');
        
        if (!$examperiod) {
            return response()->json([
                'error' => 'Exam period is required'
            ], 400);
        }

        $students = Perfomance::join('students', 'performancetbl.admno', '=', 'students.admno')
            ->select(
                'performancetbl.admno',
                DB::raw('CONCAT(students.sirname, " ", students.othername) as studentname')
            )
            ->distinct()
            ->where('performancetbl.examperiod', $examperiod)
            ->orderBy('students.sirname')
            ->get();
        
        return response()->json([
            'data' => $students
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error fetching students: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        return response()->json([
            'error' => 'Failed to fetch students: ' . $e->getMessage()
        ], 500);
    }
}

public function StudenttermlyReport(Request $request) {
    try {
        // Validation
        $validator = Validator::make($request->all(), [
            'admno' => 'required|string',
            'examperiod' => 'required|string' // Made required since it's essential for termly reports
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Get school information
        $school = Structure::first();
        
        if (!$school) {
            return response()->json([
                'error' => 'School information not found'
            ], 422);
        }

        // Get period information - Fixed variable reference
        $periodInfo = DB::table('tblperiods')->where('ID', $request->examperiod)->first();
        
        if (!$periodInfo) {
            return response()->json([
                'error' => 'Exam period not found'
            ], 404);
        }

        // Get student information
        $student = DB::table('students as s')
            ->leftJoin('tblclasses as c', 's.claid', '=', 'c.ID')
            ->leftJoin('streams as st', 'c.stid', '=', 'st.ID')
            ->select([
                's.admno',
                's.sirname',
                's.othername',
                'c.claname',
                'st.strmname',
                's.admdate',
                's.claid as class_id'
            ])
            ->where('s.admno', $request->admno)
            ->first();

        if (!$student) {
            return response()->json([
                'error' => 'Student not found'
            ], 404);
        }

        // Build performance query for ALL exams in the term
        $performance = DB::table('performancetbl as p')
            ->leftJoin('tblsubjects as sub', 'p.subid', '=', 'sub.ID')
            ->leftJoin('tblteachers as t', 'p.clateach', '=', 't.ID')
            // Join exam types for names
            ->select([
                'p.admno',
                'p.examtype',
                'p.examperiod',
                'p.examcount',
                'p.marks',
                'p.mstatus',
                'p.subid',
                'sub.sname as subject_name',
                'sub.scode as subject_code',
                'p.examtype as exam_name', // Get exam type name
                DB::raw('CONCAT(COALESCE(t.fname, ""), " ", COALESCE(t.surname, "")) as teacher_name'),
                'p.created_at'
            ])
            ->where('p.admno', $request->admno)
            ->where('p.examperiod', $request->examperiod) // Filter by exam period, not examtype
            ->where('p.mstatus', 'final') // Only show finalized marks
            ->orderBy('p.examcount', 'asc') // Order by exam sequence
            ->orderBy('sub.sname', 'asc') // Then by subject name
            ->get();

        if ($performance->isEmpty()) {
            return response()->json([
                'error' => 'No finalized performance records found for this student in the specified term'
            ], 404);
        }

        // Calculate grades and points for each record
        $performanceWithGrades = $performance->map(function ($record) {
            $record->grade = $this->calculateGrade($record->marks);
            $record->points = $this->calculatePoints($record->marks);
            return $record;
        });

        // Group by examcount (exam sequence) for better organization
        $performanceByExam = $performanceWithGrades->groupBy('examcount');

        // Calculate positions for each exam type
        $performanceWithPositions = $performanceWithGrades->map(function ($record) use ($student, $request) {
            // Get subject position for this specific exam
            $subjectRanks = DB::table('performancetbl as p')
                ->leftJoin('students as s', 'p.admno', '=', 's.admno')
                ->where('s.claid', $student->class_id)
                ->where('p.examtype', $record->examtype)
                ->where('p.examperiod', $request->examperiod)
                ->where('p.subid', $record->subid)
                ->where('p.mstatus', 'final')
                ->orderByDesc('p.marks')
                ->pluck('p.admno')
                ->toArray();

            $record->subject_position = array_search($student->admno, $subjectRanks) + 1;
            $record->subject_out_of = count($subjectRanks);

            return $record;
        });

        // Calculate overall positions for each exam type
        $examPositions = [];
        foreach ($performanceByExam as $examcount => $examRecords) {
            $examtype = $examRecords->first()->examtype;
            
            // Get overall ranks for this specific exam
            $classRanks = DB::table('performancetbl as p')
                ->leftJoin('students as s', 'p.admno', '=', 's.admno')
                ->where('s.claid', $student->class_id)
                ->where('p.examtype', $examtype)
                ->where('p.examperiod', $request->examperiod)
                ->where('p.mstatus', 'final')
                ->select('p.admno', DB::raw('SUM(p.marks) as total_marks'), DB::raw('COUNT(p.marks) as subject_count'))
                ->groupBy('p.admno')
                ->orderByDesc('total_marks')
                ->get();

            $studentRank = $classRanks->search(function ($item) use ($student) {
                return $item->admno === $student->admno;
            });

            $examPositions[$examcount] = [
                'position' => $studentRank !== false ? $studentRank + 1 : 0,
                'out_of' => $classRanks->count(),
                'exam_name' => $examRecords->first()->exam_name ?? 'Unknown',
                'total_marks' => $examRecords->sum('marks'),
                'average_marks' => $examRecords->avg('marks'),
                'subjects_count' => $examRecords->count()
            ];
        }

        // Calculate term statistics
        $termStats = $this->calculateTermStatistics($student->class_id, $request->examperiod);

        // Calculate cumulative/term average
        $allMarks = $performanceWithPositions->pluck('marks');
        $termTotalMarks = $allMarks->sum();
        $termSubjectsCount = $allMarks->count();
        $termAverageMarks = $termSubjectsCount > 0 ? round($termTotalMarks / $termSubjectsCount, 2) : 0;

        // Calculate term position (based on all exams combined)
        $termPosition = $this->calculateTermPosition($student, $request->examperiod);

        $data = [
            'school' => $school,
            'student' => $student,
            'performance' => $performanceWithPositions,
            'performanceByExam' => $performanceByExam,
            'examPositions' => $examPositions,
            'termTotalMarks' => $termTotalMarks,
            'termAverageMarks' => $termAverageMarks,
            'termSubjectsCount' => $termSubjectsCount,
            'period' => $periodInfo,
            'termStats' => $termStats,
            'termPosition' => $termPosition,
            'generatedDate' => now(),
            'studentFullName' => trim(($student->sirname ?? '') . ' ' . ($student->othername ?? ''))
        ];

        // Generate PDF
        $pdf = Pdf::loadView('reports.student_termly_report', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="termly_report_' . $request->admno . '_term_' . $request->examperiod . '.pdf"');

    } catch (\Exception $e) {
        Log::error('Error generating termly student report: ' . $e->getMessage(), [
            'admno' => $request->admno ?? 'N/A',
            'examperiod' => $request->examperiod ?? 'N/A',
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => 'Failed to generate termly student report. Please try again.',
            'details' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}
private function calculateTermStatistics($classId, $examperiod) {
    try {
        return DB::table('performancetbl as p')
            ->leftJoin('students as s', 'p.admno', '=', 's.admno')
            ->where('s.claid', $classId)
            ->where('p.examperiod', $examperiod)
            ->where('p.mstatus', 'final')
            ->selectRaw('
                COUNT(DISTINCT p.admno) as total_students,
                COUNT(DISTINCT p.examcount) as total_exams,
                ROUND(AVG(p.marks), 2) as class_average,
                MAX(p.marks) as highest_mark,
                MIN(p.marks) as lowest_mark,
                COUNT(p.marks) as total_entries
            ')
            ->first();
    } catch (\Exception $e) {
        Log::error('Error calculating term statistics: ' . $e->getMessage());
        return null;
    }
}

/**
 * Calculate student's overall term position
 */
private function calculateTermPosition($student, $examperiod) {
    try {
        // Calculate average marks per student across all exams in the term
        $classAverages = DB::table('performancetbl as p')
            ->leftJoin('students as s', 'p.admno', '=', 's.admno')
            ->where('s.claid', $student->class_id)
            ->where('p.examperiod', $examperiod)
            ->where('p.mstatus', 'final')
            ->select('p.admno', DB::raw('AVG(p.marks) as average_marks'))
            ->groupBy('p.admno')
            ->orderByDesc('average_marks')
            ->pluck('admno')
            ->toArray();

        $position = array_search($student->admno, $classAverages) + 1;
        
        return [
            'position' => $position,
            'out_of' => count($classAverages)
        ];
        
    } catch (\Exception $e) {
        Log::error('Error calculating term position: ' . $e->getMessage());
        return ['position' => 0, 'out_of' => 0];
    }
}



public function ClassPerfReport(Request $request) {
    try {
        // Validation
        $validator = Validator::make($request->all(), [
            'classid' => 'required|string',
            'exam' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Get school information
        $school = Structure::first();
        
        if (!$school) {
            return response()->json([
                'error' => 'School information not found'
            ], 422);
        }

        // Get class information
        $classInfo = DB::table('tblclasses as c')
            ->leftJoin('streams as st', 'c.stid', '=', 'st.ID')
            ->select('c.ID', 'c.claname', 'st.strmname', 'c.stid as stream_id')
            ->where('c.ID', $request->classid)
            ->first();
        
        if (!$classInfo) {
            return response()->json([
                'error' => 'Class not found'
            ], 404);
        }

        // Get all distinct subjects for this exam and class
        $subjects = DB::table('performancetbl as p')
            ->join('tblsubjects as sub', 'p.subid', '=', 'sub.ID')
            ->join('students as s', 'p.admno', '=', 's.admno')
            ->where('s.claid', $request->classid)
            ->where('p.examtype', $request->exam)
            ->where('p.mstatus', 'final')
            ->select('sub.ID', 'sub.sname', 'sub.scode')
            ->distinct()
            ->orderBy('sub.sname')
            ->get();

        if ($subjects->isEmpty()) {
            return response()->json([
                'error' => 'No subjects found for this class and exam'
            ], 404);
        }

        // Get all students in the class
        $students = DB::table('students as s')
            ->leftJoin('streams as st', 's.stream', '=', 'st.ID')
            ->where('s.claid', $request->classid)
            ->select('s.admno', 's.sirname', 's.othername', 'st.strmname', 's.stream')
            ->orderBy('s.sirname')
            ->get();

        if ($students->isEmpty()) {
            return response()->json([
                'error' => 'No students found in this class'
            ], 404);
        }

        // Build student performance data
        $studentsData = [];
        
        foreach ($students as $student) {
            $studentData = [
                'admno' => $student->admno,
                'student_name' => trim(($student->sirname ?? '') . ' ' . ($student->othername ?? '')),
                'stream_name' => $student->strmname ?? 'N/A',
                'stream_id' => $student->stream,
                'subjects' => [],
                'total_marks' => 0,
                'subjects_count' => 0,
                'average_marks' => 0,
                'average_grade' => '',
                'class_rank' => 0,
                'stream_rank' => 0,
                'join_marks' => null,
                'join_rank' => null,
                'join_examtype' => null
            ];

            // Get performance for each subject
            foreach ($subjects as $subject) {
                $performance = DB::table('performancetbl')
                    ->where('admno', $student->admno)
                    ->where('examtype', $request->exam)
                    ->where('subid', $subject->ID)
                    ->where('mstatus', 'final')
                    ->first();

                if ($performance) {
                    $grade = $this->calculateGrade($performance->marks);
                    $studentData['subjects'][$subject->ID] = [
                        'marks' => $performance->marks,
                        'grade' => $grade,
                        'display' => $performance->marks . ' ' . $grade
                    ];
                    $studentData['total_marks'] += $performance->marks;
                    $studentData['subjects_count']++;
                } else {
                    $studentData['subjects'][$subject->ID] = [
                        'marks' => null,
                        'grade' => '-',
                        'display' => '-'
                    ];
                }
            }

            // Calculate average
            if ($studentData['subjects_count'] > 0) {
                $studentData['average_marks'] = round($studentData['total_marks'] / $studentData['subjects_count'], 2);
                $studentData['average_grade'] = $this->calculateGrade($studentData['average_marks']);
            }

            // Get joining rank information
            $joinRank = DB::table('tbljoinrank')
                ->where('admno', $student->admno)
                ->where('classid', $request->classid)
                ->first();

            if ($joinRank) {
                $studentData['join_marks'] = $joinRank->Marks;
                $studentData['join_rank'] = $joinRank->rankno;
                $studentData['join_examtype'] = $joinRank->examtype;
            }

            $studentsData[] = $studentData;
        }

        // Calculate CLASS rankings (all students in class)
        $classRankings = collect($studentsData)
            ->filter(function($s) { return $s['subjects_count'] > 0; })
            ->sortByDesc('average_marks')
            ->values();

        foreach ($classRankings as $index => $rankedStudent) {
            $key = array_search($rankedStudent['admno'], array_column($studentsData, 'admno'));
            if ($key !== false) {
                $studentsData[$key]['class_rank'] = $index + 1;
            }
        }

        // Calculate STREAM rankings (group by stream)
        $studentsByStream = collect($studentsData)->groupBy('stream_id');
        
        foreach ($studentsByStream as $stream => $streamStudents) {
            $streamRankings = $streamStudents
                ->filter(function($s) { return $s['subjects_count'] > 0; })
                ->sortByDesc('average_marks')
                ->values();

            foreach ($streamRankings as $index => $rankedStudent) {
                $key = array_search($rankedStudent['admno'], array_column($studentsData, 'admno'));
                if ($key !== false) {
                    $studentsData[$key]['stream_rank'] = $index + 1;
                }
            }
        }

        // Sort final data by class rank for display
        usort($studentsData, function($a, $b) {
            if ($a['class_rank'] == 0) return 1;
            if ($b['class_rank'] == 0) return -1;
            return $a['class_rank'] - $b['class_rank'];
        });

        // Prepare data for PDF
        $data = [
            'school' => $school,
            'classInfo' => $classInfo,
            'subjects' => $subjects,
            'studentsData' => $studentsData,
            'examtype' => $request->exam,
            'generatedDate' => now()
        ];

        // Generate PDF
        $pdf = Pdf::loadView('reports.class_report', $data);
        $pdf->setPaper('A4', 'landscape'); // Landscape for wide table
        
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="class_report_' . $request->classid . '_' . $request->exam . '.pdf"');

    } catch (\Exception $e) {
        Log::error('Error generating class performance report: ' . $e->getMessage(), [
            'classid' => $request->classid ?? 'N/A',
            'exam' => $request->exam ?? 'N/A',
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => 'Failed to generate class performance report. Please try again.',
            'details' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}


public function SubjectAnalysisReport(Request $request) {
    try {
        // Validation
        $validator = Validator::make($request->all(), [
            'classid' => 'required|string',
            'exam' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Get school information
        $school = Structure::first();
        
        if (!$school) {
            return response()->json([
                'error' => 'School information not found'
            ], 422);
        }

        // Get class information
        $classInfo = DB::table('tblclasses as c')
            ->leftJoin('streams as st', 'c.stid', '=', 'st.ID')
            ->select('c.ID', 'c.claname', 'st.strmname')
            ->where('c.ID', $request->classid)
            ->first();
        
        if (!$classInfo) {
            return response()->json([
                'error' => 'Class not found'
            ], 404);
        }

        // Get all grades for distribution
        $grades = DB::table('grading')
            ->select('Grade', 'Min', 'Max')
            ->orderBy('Min', 'desc')
            ->get();

        if ($grades->isEmpty()) {
            return response()->json([
                'error' => 'Grading system not configured'
            ], 422);
        }

        // Get all subjects with performance in this class and exam
        $subjects = DB::table('performancetbl as p')
            ->join('tblsubjects as sub', 'p.subid', '=', 'sub.ID')
            ->join('students as s', 'p.admno', '=', 's.admno')
            ->where('s.claid', $request->classid)
            ->where('p.examtype', $request->exam)
            ->where('p.mstatus', 'final')
            ->select('sub.ID', 'sub.sname', 'sub.scode')
            ->distinct()
            ->orderBy('sub.sname')
            ->get();

        if ($subjects->isEmpty()) {
            return response()->json([
                'error' => 'No subjects found for this class and exam'
            ], 404);
        }

        // Analyze each subject
        $subjectAnalysis = [];
        $grandTotals = [
            'total_students' => 0,
            'total_entries' => 0,
            'mean_sum' => 0,
            'grade_distribution' => []
        ];

        // Initialize grand totals grade distribution
        foreach ($grades as $grade) {
            $grandTotals['grade_distribution'][$grade->Grade] = 0;
        }

        foreach ($subjects as $subject) {
            // Get all streams in this subject
            $streams = DB::table('performancetbl as p')
                ->join('students as s', 'p.admno', '=', 's.admno')
                ->leftJoin('streams as st', 's.stream', '=', 'st.ID')
                ->where('s.claid', $request->classid)
                ->where('p.examtype', $request->exam)
                ->where('p.subid', $subject->ID)
                ->where('p.mstatus', 'final')
                ->select('st.ID as stream_id', 'st.strmname')
                ->distinct()
                ->get();

            $subjectData = [
                'subject_name' => $subject->sname,
                'subject_code' => $subject->scode,
                'streams' => [],
                'totals' => [
                    'total_entries' => 0,
                    'students_count' => 0,
                    'mean' => 0,
                    'mean_grade' => '',
                    'grade_distribution' => []
                ]
            ];

            // Initialize subject totals grade distribution
            foreach ($grades as $grade) {
                $subjectData['totals']['grade_distribution'][$grade->Grade] = 0;
            }

            // Analyze each stream
            foreach ($streams as $stream) {
                // Get teacher for this subject and stream
                $teacher = DB::table('performancetbl as p')
                    ->join('students as s', 'p.admno', '=', 's.admno')
                    ->leftJoin('tblteachers as t', 'p.clateach', '=', 't.ID')
                    ->where('s.claid', $request->classid)
                    ->where('s.stream', $stream->stream_id)
                    ->where('p.examtype', $request->exam)
                    ->where('p.subid', $subject->ID)
                    ->where('p.mstatus', 'final')
                    ->select(DB::raw('CONCAT(COALESCE(t.fname, ""), " ", COALESCE(t.surname, "")) as teacher_name'))
                    ->first();

                // Get performance data for this stream
                $performance = DB::table('performancetbl as p')
                    ->join('students as s', 'p.admno', '=', 's.admno')
                    ->where('s.claid', $request->classid)
                    ->where('s.stream', $stream->stream_id)
                    ->where('p.examtype', $request->exam)
                    ->where('p.subid', $subject->ID)
                    ->where('p.mstatus', 'final')
                    ->select('p.marks')
                    ->get();

                $streamData = [
                    'stream_name' => $stream->strmname ?? 'N/A',
                    'teacher_name' => trim($teacher->teacher_name ?? 'Not Assigned'),
                    'total_entries' => $performance->count(),
                    'mean' => 0,
                    'mean_grade' => '',
                    'grade_distribution' => []
                ];

                // Initialize grade distribution for stream
                foreach ($grades as $grade) {
                    $streamData['grade_distribution'][$grade->Grade] = 0;
                }

                // Calculate mean and grade distribution
                if ($performance->count() > 0) {
                    $totalMarks = $performance->sum('marks');
                    $streamData['mean'] = round($totalMarks / $performance->count(), 4);
                    $streamData['mean_grade'] = $this->calculateGrade($streamData['mean']);

                    // Count grade distribution
                    foreach ($performance as $perf) {
                        $grade = $this->calculateGrade($perf->marks);
                        if (isset($streamData['grade_distribution'][$grade])) {
                            $streamData['grade_distribution'][$grade]++;
                            $subjectData['totals']['grade_distribution'][$grade]++;
                            $grandTotals['grade_distribution'][$grade]++;
                        }
                    }
                }

                // Update subject totals
                $subjectData['totals']['total_entries'] += $streamData['total_entries'];
                $subjectData['totals']['students_count'] += $streamData['total_entries'];
                $subjectData['totals']['mean'] += $streamData['mean'];

                $subjectData['streams'][] = $streamData;
            }

            // Calculate subject average mean
            if (count($subjectData['streams']) > 0) {
                $subjectData['totals']['mean'] = round($subjectData['totals']['mean'] / count($subjectData['streams']), 4);
                $subjectData['totals']['mean_grade'] = $this->calculateGrade($subjectData['totals']['mean']);
            }

            // Update grand totals
            $grandTotals['total_entries'] += $subjectData['totals']['total_entries'];
            $grandTotals['mean_sum'] += $subjectData['totals']['mean'];

            $subjectAnalysis[] = $subjectData;
        }

        // Calculate grand average mean
        if (count($subjectAnalysis) > 0) {
            $grandTotals['mean'] = round($grandTotals['mean_sum'] / count($subjectAnalysis), 4);
            $grandTotals['mean_grade'] = $this->calculateGrade($grandTotals['mean']);
        }

        // Prepare data for PDF
        $data = [
            'school' => $school,
            'classInfo' => $classInfo,
            'examtype' => $request->exam,
            'grades' => $grades,
            'subjectAnalysis' => $subjectAnalysis,
            'grandTotals' => $grandTotals,
            'generatedDate' => now()
        ];

        // Generate PDF
        $pdf = Pdf::loadView('reports.subject_analysis_report', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="subject_analysis_' . $request->classid . '_' . $request->exam . '.pdf"');

    } catch (\Exception $e) {
        Log::error('Error generating subject analysis report: ' . $e->getMessage(), [
            'classid' => $request->classid ?? 'N/A',
            'exam' => $request->exam ?? 'N/A',
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => 'Failed to generate subject analysis report. Please try again.',
            'details' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}
// Helper method for calculating grade
private function calculateGrade($marks) {
    $grade = DB::table('grading')
        ->where('Min', '<=', $marks)
        ->where('Max', '>=', $marks)
        ->first();
    
    return $grade ? $grade->Grade : 'N/A';
}
}
