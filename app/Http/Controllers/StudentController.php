<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Feeassign;
use App\Models\Branches;
use App\Models\Classes;
use App\Models\Parents;
use App\Models\Streams;
use App\Models\Structure;
use App\Models\Houses;
use App\Models\Managefee;
use App\Models\Feeitems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller {
    public function create()
    {
        return view('students.add_student');
    }
    public function index()
    {
        return view('students.man_student');
    }
    public function index1()
    {
        return view('students.rep_student');
    }

    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'admno' => 'required|string|max:255',
                'sirname' => 'required|string|max:255',
                'othername' => 'required|string|max:255',
                'gender' => 'required|string|max:255',
                'dateob' => 'required|string|max:255',
                'admdate' => 'required|string|max:255',
                'caid' => 'required|string|max:255',
                'claid' => 'required|string|max:255',
                'stream' => 'required|string|max:255',
                'border' => 'required|string|max:255',
                'houseid' => 'nullable|string|max:255',
                'parent' => 'required|string|max:255',
                'photo' => 'nullable|image|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if fee assignments exist for the class
            $feeAssignments = Feeassign::where('classid', $request->claid)->get();
            if ($feeAssignments->isEmpty()) {
                return response()->json([
                    'errors' => ['claid' => ['The selected class has no fee items']]
                ], 422);
            }

            // Handle boarding fee logic
            if ($request->border === 'yes') {
                if (empty($request->houseid)) {
                    return response()->json([
                        'errors' => ['houseid' => ['House ID is required for boarding students']]
                    ], 422);
                }

                // Check if the selected house has a fee amount
                $houseFeesExist = DB::table('feeitems')->where('house', $request->houseid)->exists();
                if (!$houseFeesExist) {
                    return response()->json([
                        'errors' => ['houseid' => ['The selected house has no amount']]
                    ], 422);
                }
            }

            // Get the active period ID
            $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->value('ID');
            if (!$activePeriod) {
                return response()->json([
                    'errors' => ['general' => ['No active period found']]
                ], 422);
            }

            $path = null;
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('students', 'public');
            }

            // Check if parent exists and get siblings
            $siblings = Student::where('parent', $request->parent)->pluck('admno')->toArray();

            // Create the student record
            $student = Student::create([
                'admno' => $request->admno,
                'sirname' => $request->sirname,
                'othername' => $request->othername,
                'gender' => $request->gender,
                'dateob' => $request->dateob,
                'admdate' => $request->admdate,
                'caid' => $request->caid,
                'claid' => $request->claid,
                'stream' => $request->stream,
                'border' => $request->border,
                'houseid' => $request->border === 'yes' ? $request->houseid : null,
                'parent' => $request->parent,
                'sibling' => !empty($siblings) ? implode(',', $siblings) : null,
                'photo' => $path,
            ]);

            // Create a Managefee record for each fee assignment
            foreach ($feeAssignments as $feeAssignment) {
                // Use the DB facade to insert directly to ensure all fields are included
                DB::table('managefee')->insert([
                    'admno' => $student->admno,
                    'classid' => $feeAssignment->classid,
                    'feeid' => $feeAssignment->feeid,
                    'amount' => $feeAssignment->feeamount,
                    'paid' => 0,
                    'balance' => $feeAssignment->feeamount,
                    'status' => 'Pending',
                    'period' => $activePeriod,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Add boarding fees if student is a boarder
            if ($request->border === 'yes') {
                $boardingFees = DB::table('feeitems')->where('house', $request->houseid)->get();
                foreach ($boardingFees as $boardingFee) {
                    DB::table('managefee')->insert([
                        'admno' => $student->admno,
                        'classid' => $request->claid,
                        'feeid' => $boardingFee->ID,
                        'amount' => $boardingFee->amount,
                        'paid' => 0,
                        'balance' => $boardingFee->amount,
                        'status' => 'Pending',
                        'period' => $activePeriod,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            return response()->json([
                'message' => 'Student added successfully with fee assignments!'
            ]);

        } catch (\Exception $e) {
            
            Log::error('Student creation error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            
            return response()->json([
                'error' => 'Failed to add student: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
 * Get statistics for a specific campus
 * 
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function getCampusStats(Request $request)
{
    try {
        $campusId = $request->input('campus_id');
        
        if (!$campusId) {
            return response()->json([
                'error' => 'Campus ID is required'
            ], 400);
        }
        
        // Get count of houses for this campus
        $houses = DB::table('houses')
            ->select('houses.ID', 'houses.housen')
            ->where('houses.brid', $campusId)
            ->get();
        
        $houseCount = $houses->count();
        
        // Get count of classes for this campus
        $classes = DB::table('tblclasses')
            ->select('tblclasses.ID', 'tblclasses.claname')
            ->where('tblclasses.caid', $campusId)
            ->get();
        
        $classCount = $classes->count();
        
        // Get student counts for each house
        $houseStats = [];
        foreach ($houses as $house) {
            $studentCount = DB::table('students')
                ->where('houseid', $house->ID)
                ->count();
                
            $houseStats[] = [
                'id' => $house->ID,
                'name' => $house->housen,
                'student_count' => $studentCount
            ];
        }
        
        // Get student counts for each class
        $classStats = [];
        foreach ($classes as $class) {
            $studentCount = DB::table('students')
                ->where('claid', $class->ID)
                ->count();
                
            $classStats[] = [
                'id' => $class->ID,
                'name' => $class->claname,
                'student_count' => $studentCount
            ];
        }
        
        // Get total student count for this campus
        $totalStudents = DB::table('students')
            ->join('tblclasses', 'students.claid', '=', 'tblclasses.ID')
            ->where('tblclasses.caid', $campusId)
            ->count();
        
            $latestStudentID = DB::table('students')
            ->max('students.StudentID');
            
        return response()->json([
            'house_count' => $houseCount,
            'class_count' => $classCount,
            'house_stats' => $houseStats,
            'class_stats' => $classStats,
            'total_students' => $totalStudents,
            'latest_student_id' => $latestStudentID ?? 0
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error fetching campus stats: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        
        return response()->json([
            'error' => 'Failed to fetch campus statistics: ' . $e->getMessage()
        ], 500);
    }
}

public function postnewitem(Request $request)
{
    try {
        // Validate the request
        $request->validate([
            'admno' => 'required|string',
            'feeid' => 'required|integer',
            'famount' => 'required|numeric|min:0',
            'classid' => 'required|integer',
        ]);

        // Get the active period ID
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->value('ID');
        if (!$activePeriod) {
            return response()->json([
                'success' => false,
                'error' => 'No active period found'
            ], 422);
        }

        // Check if the fee item already exists for this student in the active period
        $existingFee = DB::table('managefee')
            ->where('admno', $request->admno)
            ->where('feeid', $request->feeid)
            ->where('period', $activePeriod)
            ->first();

        if ($existingFee) {
            return response()->json([
                'success' => false,
                'error' => 'This fee item already exists for the student in the current period'
            ], 409); 
        }

        // Verify that the student exists
        $student = DB::table('students')->where('admno', $request->admno)->first();
        if (!$student) {
            return response()->json([
                'success' => false,
                'error' => 'Student not found'
            ], 404);
        }

        // Insert the new fee record
        DB::table('managefee')->insert([
            'admno' => $request->admno,
            'classid' => $request->classid,
            'feeid' => $request->feeid,
            'amount' => $request->famount,
            'paid' => 0,
            'balance' => $request->famount,
            'status' => 'Pending',
            'period' => $activePeriod,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fee item posted successfully'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        
        Log::error('Error posting fee item: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'error' => 'An error occurred while posting the fee item'
        ], 500);
    }
}

public function postpercampus(Request $request) {
    try {
        // Validate the request
        $request->validate([
            'caid' => 'required|string',
            'feeid' => 'required|integer',
            'famount' => 'required|numeric|min:0',
        ]);

        // Get the active period ID
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->value('ID');
        if (!$activePeriod) {
            return response()->json([
                'success' => false,
                'error' => 'No active period found'
            ], 422);
        }

        // Get all students for the selected campus
        $studentsInCampus = DB::table('students')
        ->select('admno', 'claid')
        ->when($request->caid !== '0', function($query) use ($request) {
            return $query->where('caid', $request->caid);
        })
        ->get();

        if ($studentsInCampus->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => 'No students found for selected campus'
            ], 422);
        }

        // Get existing fee records for these students to avoid duplicates
        $studentAdmNos = $studentsInCampus->pluck('admno');
        $existingFees = DB::table('managefee')
            ->where('feeid', $request->feeid)
            ->where('period', $activePeriod)
            ->whereIn('admno', $studentAdmNos)
            ->pluck('admno')
            ->toArray();

        $newFeeRecords = [];
        $skippedCount = 0;
        $processedCount = 0;

        // Process each student
        foreach ($studentsInCampus as $student) {
            // Skip if student already has this fee item
            if (in_array($student->admno, $existingFees)) {
                $skippedCount++;
                continue;
            }

            // Prepare fee record for insertion
            $newFeeRecords[] = [
                'admno' => $student->admno,
                'classid' => $student->claid,
                'feeid' => $request->feeid,
                'amount' => $request->famount,
                'paid' => 0,
                'balance' => $request->famount,
                'status' => 'Pending',
                'period' => $activePeriod,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $processedCount++;
        }

       DB::transaction(function() use ($newFeeRecords) {
    if (!empty($newFeeRecords)) {
        DB::table('managefee')->insert($newFeeRecords);
    }
});

        return response()->json([
            'success' => true,
            'message' => "Fee item posted successfully for {$processedCount} students. {$skippedCount} students were skipped (already have this fee item).",
            'processed' => $processedCount,
            'skipped' => $skippedCount,
            'total_students' => $studentsInCampus->count()
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        
        Log::error('Error posting fee item per campus: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'error' => 'An error occurred while posting the fee item'
        ], 500);
    }
}




public function postperhouse(Request $request) {
    try {
        // Validate the request
        $request->validate([
            'houseid' => 'required|string',
            'feeid' => 'required|integer',
            'famount' => 'required|numeric|min:0',
        ]);

        // Get the active period ID
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->value('ID');
        if (!$activePeriod) {
            return response()->json([
                'success' => false,
                'error' => 'No active period found'
            ], 422);
        }

        // Get all students for the selected campus
        $studentsInCampus = DB::table('students')
        ->select('admno', 'houseid')
        ->when($request->houseid !== '0', function($query) use ($request) {
            return $query->where('houseid', $request->houseid);
        })
        ->get();

        if ($studentsInCampus->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => 'No students found for selected campus'
            ], 422);
        }

        // Get existing fee records for these students to avoid duplicates
        $studentAdmNos = $studentsInCampus->pluck('admno');
        $existingFees = DB::table('managefee')
            ->where('feeid', $request->feeid)
            ->where('period', $activePeriod)
            ->whereIn('admno', $studentAdmNos)
            ->pluck('admno')
            ->toArray();

        $newFeeRecords = [];
        $skippedCount = 0;
        $processedCount = 0;

        // Process each student
        foreach ($studentsInCampus as $student) {
            // Skip if student already has this fee item
            if (in_array($student->admno, $existingFees)) {
                $skippedCount++;
                continue;
            }

            // Prepare fee record for insertion
            $newFeeRecords[] = [
                'admno' => $student->admno,
                'classid' => $student->claid,
                'feeid' => $request->feeid,
                'amount' => $request->famount,
                'paid' => 0,
                'balance' => $request->famount,
                'status' => 'Pending',
                'period' => $activePeriod,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $processedCount++;
        }

       DB::transaction(function() use ($newFeeRecords) {
    if (!empty($newFeeRecords)) {
        DB::table('managefee')->insert($newFeeRecords);
    }
});

        return response()->json([
            'success' => true,
            'message' => "Fee item posted successfully for {$processedCount} students. {$skippedCount} students were skipped (already have this fee item).",
            'processed' => $processedCount,
            'skipped' => $skippedCount,
            'total_students' => $studentsInCampus->count()
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        
        Log::error('Error posting fee item per campus: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'error' => 'An error occurred while posting the fee item'
        ], 500);
    }
}

public function postperclass(Request $request) {
    try {
        // Validate the request
        $request->validate([
            'claid' => 'required|string',
            'feeid' => 'required|integer',
            'famount' => 'required|numeric|min:0',
        ]);

        // Get the active period ID
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->value('ID');
        if (!$activePeriod) {
            return response()->json([
                'success' => false,
                'error' => 'No active period found'
            ], 422);
        }

        // Get all students for the selected campus
        $studentsInCampus = DB::table('students')
        ->select('admno', 'claid')
        ->when($request->claid !== '0', function($query) use ($request) {
            return $query->where('claid', $request->claid);
        })
        ->get();

        if ($studentsInCampus->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => 'No students found for selected campus'
            ], 422);
        }

        // Get existing fee records for these students to avoid duplicates
        $studentAdmNos = $studentsInCampus->pluck('admno');
        $existingFees = DB::table('managefee')
            ->where('feeid', $request->feeid)
            ->where('period', $activePeriod)
            ->whereIn('admno', $studentAdmNos)
            ->pluck('admno')
            ->toArray();

        $newFeeRecords = [];
        $skippedCount = 0;
        $processedCount = 0;

        // Process each student
        foreach ($studentsInCampus as $student) {
            // Skip if student already has this fee item
            if (in_array($student->admno, $existingFees)) {
                $skippedCount++;
                continue;
            }

            // Prepare fee record for insertion
            $newFeeRecords[] = [
                'admno' => $student->admno,
                'classid' => $student->claid,
                'feeid' => $request->feeid,
                'amount' => $request->famount,
                'paid' => 0,
                'balance' => $request->famount,
                'status' => 'Pending',
                'period' => $activePeriod,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $processedCount++;
        }

       DB::transaction(function() use ($newFeeRecords) {
    if (!empty($newFeeRecords)) {
        DB::table('managefee')->insert($newFeeRecords);
    }
});

        return response()->json([
            'success' => true,
            'message' => "Fee item posted successfully for {$processedCount} students. {$skippedCount} students were skipped (already have this fee item).",
            'processed' => $processedCount,
            'skipped' => $skippedCount,
            'total_students' => $studentsInCampus->count()
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        
        Log::error('Error posting fee item per campus: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'error' => 'An error occurred while posting the fee item'
        ], 500);
    }
}


public function getData(Request $request)
{
    try {
       
       

        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $searchValue = $request->get('search')['value'] ?? '';
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';

        

        // Column mapping for ordering
        $columns = [
            0 => 'sirname',
            1 => 'admno',
            2 => 'StudentID',
            3 => 'Gender',
            4 => 'admdate',
            5 => 'claid',
            6 => 'status'
        ];

        // ✅ Base query with relationships
        $query = Student::select(
                'students.*',
                'tblclasses.claname',
                'branches.branchname'
            )
            ->leftJoin('tblclasses', 'students.claid', '=', 'tblclasses.ID')
            ->leftJoin('branches', 'students.caid', '=', 'branches.ID');
           // ->where('tblemployees.emp_id', '!=', '1');

        
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('students.sirname', 'like', "%{$searchValue}%")
                  ->orWhere('students.othername', 'like', "%{$searchValue}%")
                  ->orWhere('students.admno', 'like', "%{$searchValue}%")
                  ->orWhere('students.StudentID', 'like', "%{$searchValue}%")
                  ->orWhere('students.gender', 'like', "%{$searchValue}%")
                  ->orWhere('tblclasses.claname', 'like', "%{$searchValue}%")
                  ->orWhere('branches.branchname', 'like', "%{$searchValue}%")
                  ->orWhere('students.status', 'like', "%{$searchValue}%");
            });

            Log::info('Students getData: Search applied', [
                'searchValue' => $searchValue
            ]);
        }

        // Get total records before pagination
        $totalRecords = Student::where('admno', '!=', '1')->count();
        $filteredRecords = $query->count();

        Log::info('AgentsController getData: Record counts', [
            'totalRecords' => $totalRecords,
            'filteredRecords' => $filteredRecords
        ]);

        // Apply ordering
        $orderColumnName = $columns[$orderColumn] ?? 'admno';
        $query->orderBy($orderColumnName, $orderDir);

        
        // Apply pagination
        $students = $query->skip($start)->take($length)->get();

       

        // Format data for DataTable
        $data = [];
        foreach ($students as $student) {
            $studentData = [
                'full_name' => $student->sirname . ' ' . $student->othername,
                'profile_photo' => $student->photo,
                'admno' => $student->admno,
                'StudentID' => $student->StudentID,
                'gender' => $student->gender ?? 'N/A',
                'admdate' => $student->admdate ?? 'N/A',
                'claname' => $student->claname ?? 'N/A',
                'status' => $student->status,
                'actions' => $student->admno
            ];
            
            $data[] = $studentData;
        }

     

        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];

        

        return response()->json($response);

    } catch (\Exception $e) {
        Log::error('AgentsController getData error', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'draw' => $request->get('draw', 1),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => 'Error loading data: ' . $e->getMessage()
        ], 500);
    }
}


public function editstudent($id)
{
    try {
        // Eager load the registration relationship
       

        $student = Student::findOrFail($id);
        
        
        // Build response array with proper null handling
        $studentData = [
            'admno' => $student->admno,
            'StudentID' => $student->StudentID,
            'sirname' => $student->sirname,
            'othername' => $student->othername,
            'gender' => $student->gender,
            'dateob' => $student->dateob,
            'admdate' => $student->admdate,
            'caid' => $student->caid,
            'claid' => $student->claid,
            'stream' => $student->stream,
            'border' => $student->border,
            'houseid' => $student->houseid,
            'parent' => $student->parent,
            'sibling' => $student->sibling,
            'photo' => $student->photo
        ];
        
        
        return response()->json([
            'status' => 'success',
            'student' => $studentData
        ]);
        
    }  catch (\Exception $e) {
        Log::error('Failed to load agent for editing', [
            'user_id' => $id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to load user data. Please try again.'
        ], 500);
    }
}

public function update(Request $request, $id)
{
    try {
        Log::info("Update request received for student: " . $id);
        Log::info("Request data:", $request->all());

        $student = Student::where('StudentID', $id)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'admno' => 'required|string|max:255',
            'sirname' => 'required|string|max:255',
            'othername' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'dateob' => 'required|string|max:255',
            'admdate' => 'required|string|max:255',
            'caid' => 'required|string|max:255',
            'claid' => 'required|string|max:255',
            'stream' => 'required|string|max:255',
            'border' => 'required|string|max:255',
            'houseid' => 'nullable|string|max:255',
            'parent' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if fee assignments exist for the class
        $feeAssignments = Feeassign::where('classid', $request->claid)->get();
        if ($feeAssignments->isEmpty()) {
            return response()->json([
                'errors' => ['claid' => ['The selected class has no fee items']]
            ], 422);
        }

        // Boarding validation logic
        if ($request->border === 'yes') {
            if (empty($request->houseid)) {
                return response()->json([
                    'errors' => ['houseid' => ['House ID is required for boarding students']]
                ], 422);
            }

            $houseFeesExist = DB::table('feeitems')->where('house', $request->houseid)->exists();
            if (!$houseFeesExist) {
                return response()->json([
                    'errors' => ['houseid' => ['The selected house has no amount']]
                ], 422);
            }
        }

        // Active period check
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->value('ID');
        if (!$activePeriod) {
            return response()->json([
                'errors' => ['general' => ['No active period found']]
            ], 422);
        }

        // Handle photo update
        $path = $student->photo;
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('students', 'public');
        }

        // Get siblings
        $siblings = Student::where('parent', $request->parent)
            ->where('StudentID', '!=', $student->StudentID)
            ->pluck('admno')
            ->toArray();

        // Keep old values to detect changes
        $oldClass = $student->claid;
        $oldBorder = $student->border;
        $oldHouse = $student->houseid;

        // Update student record
        $student->update([
            'admno' => $request->admno,
            'sirname' => $request->sirname,
            'othername' => $request->othername,
            'gender' => strtolower($request->gender),
            'dateob' => $request->dateob,
            'admdate' => $request->admdate,
            'caid' => $request->caid,
            'claid' => $request->claid,
            'stream' => $request->stream,
            'border' => strtolower($request->border),
            'houseid' => ($request->border === 'yes') ? $request->houseid : null,
            'parent' => $request->parent,
            'sibling' => !empty($siblings) ? implode(',', $siblings) : null,
            'photo' => $path,
        ]);

        // ============================
        // Update Managefee Logic
        // ============================

        // If class changed, remove old class fee assignments and insert new ones
        if ($oldClass != $request->claid) {

            DB::table('managefee')
                ->where('admno', $student->admno)
                ->where('classid', $oldClass)
                ->where('period', $activePeriod)
                ->delete();

            foreach ($feeAssignments as $feeAssignment) {
                DB::table('managefee')->insert([
                    'admno' => $student->admno,
                    'classid' => $feeAssignment->classid,
                    'feeid' => $feeAssignment->feeid,
                    'amount' => $feeAssignment->feeamount,
                    'paid' => 0,
                    'balance' => $feeAssignment->feeamount,
                    'status' => 'Pending',
                    'period' => $activePeriod,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // Handle boarding fee updates
        if ($oldBorder == 'yes' && $request->border == 'no') {
            // remove boarding fee items
            DB::table('managefee')
                ->where('admno', $student->admno)
                ->whereIn('feeid', function ($query) use ($oldHouse) {
                    $query->select('ID')
                          ->from('feeitems')
                          ->where('house', $oldHouse);
                })
                ->where('period', $activePeriod)
                ->delete();
        }

        if ($request->border == 'yes') {
            // if boarding enabled OR house changed
            if ($oldBorder == 'no' || $oldHouse != $request->houseid) {

                // delete old boarding fees first
                DB::table('managefee')
                    ->where('admno', $student->admno)
                    ->whereIn('feeid', function ($query) use ($oldHouse) {
                        $query->select('ID')
                              ->from('feeitems')
                              ->where('house', $oldHouse);
                    })
                    ->where('period', $activePeriod)
                    ->delete();

                // add new boarding fees
                $boardingFees = DB::table('feeitems')->where('house', $request->houseid)->get();

                foreach ($boardingFees as $boardingFee) {
                    DB::table('managefee')->insert([
                        'admno' => $student->admno,
                        'classid' => $request->claid,
                        'feeid' => $boardingFee->ID,
                        'amount' => $boardingFee->amount,
                        'paid' => 0,
                        'balance' => $boardingFee->amount,
                        'status' => 'Pending',
                        'period' => $activePeriod,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Student updated successfully',
            'data' => $student
        ]);

    } catch (\Exception $e) {
        Log::error('Student update failed:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update student: ' . $e->getMessage()
        ], 500);
    }
}

public function getReport($admno)
{
    try {
        // Get student with relationships
        $student = Student::where('admno', $admno)->first();
        
        if (!$student) {
            return response()->json([
                'error' => 'Student not found'
            ], 404);
        }

        // Get related data
        $branch = Branches::find($student->caid);
        $class = Classes::find($student->claid);
        $stream = Streams::find($student->stream);
        $house = $student->houseid ? Houses::find($student->houseid) : null;
        $parent = Parents::find($student->parent);
        
        // Get school information
        $school = Structure::first();
        
        if (!$school) {
            return response()->json([
                'error' => 'School information not found'
            ], 422);
        }

        // Get fee information
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->first();
        $fees = DB::table('managefee')
            ->join('feeitems', 'managefee.feeid', '=', 'feeitems.ID')
            ->where('managefee.admno', $admno)
            ->where('managefee.period', $activePeriod->ID ?? null)
            ->select(
                'feeitems.feename',
                'managefee.amount',
                'managefee.paid',
                'managefee.balance',
                'managefee.status'
            )
            ->get();

        // Calculate totals
        $totalFees = $fees->sum('amount');
        $totalPaid = $fees->sum('paid');
        $totalBalance = $fees->sum('balance');

        // Get siblings
        // Get siblings
$siblings = collect([]); // Initialize as an empty collection
if ($student->sibling) {
    $siblingAdmnos = explode(',', $student->sibling);
    $siblings = Student::whereIn('admno', $siblingAdmnos)->get();
}

        // Generate HTML
        $html = view('students.report_template', compact(
            'student',
            'branch',
            'class',
            'stream',
            'house',
            'parent',
            'school',
            'fees',
            'totalFees',
            'totalPaid',
            'totalBalance',
            'siblings',
            'activePeriod'
        ))->render();

        return response()->json([
            'html' => $html
        ]);

    } catch (\Exception $e) {
        Log::error('Student report error: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'Failed to generate report: ' . $e->getMessage()
        ], 500);
    }
}

public function getAllReport(Request $request)
{
    try {
        $groupBy = $request->input('group_by', 'class');
        $classId = $request->input('class_id');
        $branchId = $request->input('branch_id');

        // Get school information
        $school = Structure::first();
        
        if (!$school) {
            return response()->json([
                'error' => 'School information not found'
            ], 422);
        }

        // Build query
        $query = Student::query();
        
        if ($classId) {
            $query->where('claid', $classId);
        }
        
        if ($branchId) {
            $query->where('caid', $branchId);
        }

        // Get students with related data
        $students = $query->get();

        // Group students
        $groupedStudents = [];
        $groupNames = [];
        
        switch ($groupBy) {
            case 'class':
                foreach ($students as $student) {
                    $class = Classes::find($student->claid);
                    $groupKey = $class ? $class->ID : 'Unknown';
                    $groupName = $class ? $class->claname : 'Unknown Class';
                    $groupedStudents[$groupKey][] = $student;
                    $groupNames[$groupKey] = $groupName;
                }
                break;
                
            case 'branch':
                foreach ($students as $student) {
                    $branch = Branches::find($student->caid);
                    $groupKey = $branch ? $branch->ID : 'Unknown';
                    $groupName = $branch ? $branch->branchname : 'Unknown Branch';
                    $groupedStudents[$groupKey][] = $student;
                    $groupNames[$groupKey] = $groupName;
                }
                break;
                
            case 'gender':
                foreach ($students as $student) {
                    $groupKey = $student->gender;
                    $groupName = ucfirst($student->gender);
                    $groupedStudents[$groupKey][] = $student;
                    $groupNames[$groupKey] = $groupName;
                }
                break;
                
            case 'border':
                foreach ($students as $student) {
                    $groupKey = $student->border;
                    $groupName = ucfirst($student->border) . ' Students';
                    $groupedStudents[$groupKey][] = $student;
                    $groupNames[$groupKey] = $groupName;
                }
                break;
                
            case 'house':
                foreach ($students as $student) {
                    if ($student->border === 'yes' && $student->houseid) {
                        $house = Houses::find($student->houseid);
                        $groupKey = $house ? $house->ID : 'No House';
                        $groupName = $house ? $house->housen : 'No House';
                    } else {
                        $groupKey = 'day_scholars';
                        $groupName = 'Day Scholars';
                    }
                    $groupedStudents[$groupKey][] = $student;
                    $groupNames[$groupKey] = $groupName;
                }
                break;
                
            case 'none':
                $groupedStudents['all'] = $students->toArray();
                $groupNames['all'] = 'All Students';
                break;
        }

        // Calculate statistics
        $totalStudents = $students->count();
        $maleCount = $students->where('gender', 'male')->count();
        $femaleCount = $students->where('gender', 'female')->count();
        $boardersCount = $students->where('border', 'yes')->count();
        $dayScholarsCount = $students->where('border', 'no')->count();

        // Get all classes and branches for filters
        $allClasses = Classes::all();
        $allBranches = Branches::all();

        // Generate HTML
        $html = view('students.all_report_template', compact(
            'school',
            'groupedStudents',
            'groupNames',
            'groupBy',
            'totalStudents',
            'maleCount',
            'femaleCount',
            'boardersCount',
            'dayScholarsCount'
        ))->render();

        return response()->json([
            'html' => $html,
            'classes' => $allClasses,
            'branches' => $allBranches
        ]);

    } catch (\Exception $e) {
        Log::error('All students report error: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'Failed to generate report: ' . $e->getMessage()
        ], 500);
    }
}

public function exportStudents(Request $request)
{
    try {
        $groupBy = $request->input('group_by', 'class');
        $classId = $request->input('class_id');
        $branchId = $request->input('branch_id');

        // Build query
        $query = Student::query();
        
        if ($classId) {
            $query->where('claid', $classId);
        }
        
        if ($branchId) {
            $query->where('caid', $branchId);
        }

        $students = $query->get();

        // Create CSV
        $filename = 'students_report_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Admission No',
                'Surname',
                'Other Name',
                'Gender',
                'Date of Birth',
                'Admission Date',
                'Branch',
                'Class',
                'Stream',
                'Boarding Status',
                'House'
            ]);

            // Add data
            foreach ($students as $student) {
                $branch = Branches::find($student->caid);
                $class = Classes::find($student->claid);
                $stream = Streams::find($student->stream);
                $house = $student->houseid ? Houses::find($student->houseid) : null;

                fputcsv($file, [
                    $student->admno,
                    $student->sirname,
                    $student->othername,
                    ucfirst($student->gender),
                    $student->dateob,
                    $student->admdate,
                    $branch ? $branch->branchname : 'N/A',
                    $class ? $class->claname : 'N/A',
                    $stream ? $stream->strmname : 'N/A',
                    ucfirst($student->border),
                    $house ? $house->housen : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

    } catch (\Exception $e) {
        Log::error('Export students error: ' . $e->getMessage());
        return back()->with('error', 'Failed to export students');
    }
}

}