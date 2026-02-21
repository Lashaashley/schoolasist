<?php

namespace App\Http\Controllers;

use App\Models\Jranks;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class JranksController extends Controller
{
    public function create()
{
    $streams = Jranks::distinct()->get(['ID', 'strmname','admno', 'classid', 'stream', 'examtype', 'examyear', 'Marks']);
    dd($streams); // Debug data
    return view('students.examtypes', compact('jranks'));
}


public function store(Request $request)
{
    Log::info('Incoming request data:', $request->all());

    $validator = Validator::make($request->all(), [
        'admno'     => 'required|string|exists:students,admno',
        'examtype'  => 'required|string|max:255',
        'examyear'  => 'required|string|max:255',
        'marks'     => 'required|numeric|min:0|max:10000',
    ]);

    if ($validator->fails()) {
        Log::warning('Validation errors:', $validator->errors()->toArray());
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        DB::beginTransaction();

        // 🧠 Get student's class & stream
        $student = Student::where('admno', $request->admno)->first();
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $classId = $student->claid;
        $stream  = $student->stream;

        // 🧩 Insert or update the student marks
        $record = Jranks::updateOrCreate(
    [
        'admno'     => $request->admno,
        'examtype'  => $request->examtype,
        'examyear'  => $request->examyear,
    ],
    [
        'classid'   => $classId,
        'stream'    => $stream,
        'Marks'     => $request->marks,
        'rankno'    => 0, // ✅ Temporary placeholder rank
    ]
);


        // 🧮 Recalculate all ranks for this class, examtype, and examyear
        $students = Jranks::where('classid', $classId)
            //->where('examtype', $request->examtype)
            //->where('examyear', $request->examyear)
            ->orderByDesc('Marks')
            ->get();

        $rank = 1;
        foreach ($students as $stu) {
            $stu->update(['rankno' => $rank]);
            $rank++;
        }

        DB::commit();

        return response()->json(['message' => 'Joining performance and ranks updated successfully!']);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error saving rank: ' . $e->getMessage());
        return response()->json(['error' => 'Error saving rank: ' . $e->getMessage()], 500);
    }
}





public function getstudents2(Request $request)
{
    try {
        
        
        $students = DB::table('students')
            ->select(
                'admno', 
                DB::raw('CONCAT(sirname, " ", othername) as studentname')
            )
            //->where('claid', $classId)
            ->orderBy('sirname')
            ->get();
        
        return response()->json($students);
        
    } catch (\Exception $e) {
        Log::error('Error fetching students: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        return response()->json([
            'error' => 'Failed to fetch students: ' . $e->getMessage()
        ], 500);
    }
}









}

