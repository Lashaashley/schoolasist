<?php

namespace App\Http\Controllers;

use App\Models\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubjectsController extends Controller
{
    public function create()
    {
        return view('students.add_subject');
    }

    public function store(Request $request)
    {
        try { 
            $validator = Validator::make($request->all(), [
                'sname' => 'required|string|max:255',
                'scode' => 'required|string|unique:tblsubjects,scode', // Note: Did you mean 'type' instead of 'typpe'?
                'sdept' => 'required|string|max:255',
                'isall' => 'nullable|string|max:255',
            ]); 
            
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            Subjects::create(array_merge(
                $validator->validated()
            ));
            
            return response()->json([
                'message' => 'Subject added successfully'
            ], 201); // Added proper HTTP status code for creation

        } catch (\Exception $e) {
            Log::error('Student creation error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function manage()
    {
        return view('students.sumanage');
    }

    // Add this method to fetch parents data for DataTables
    public function getteachers()
    {
        $teachers = Subjects::all();
        return response()->json(['data' => $teachers]);
    }
    public function getAllsubjects(Request $request)
{
     $subjects = DB::table('tblsubjects')
        ->select('ID', 'sname', 'isall')
        ->orderBy('sname')
        ->get();

    $assigned = [];
    if ($request->filled('classid')) {
        // tblsubassign has: subid, classid, classtr (teacher id)
        $assigned = DB::table('tblsubassign')
            ->where('classid', $request->classid)
            ->pluck('classtr', 'subid') // [subid => teacherid]
            ->toArray();
    }

    return response()->json([
        'data' => $subjects,
        'assigned' => $assigned,
    ]);
}
    public function getAllparents()
{
    $teachers = Subjects::select('ID', 
                          DB::raw("CONCAT(fname, ' ', surname) as teachername"), 
                          'fname', 
                          'surname')
                    ->orderBy('surname')
                    ->get();
    
    return response()->json([
        'data' => $teachers,
    ]);
}

public function getHODs(Request $request) {
    $type = 'HOD';
    
    // Fetch classes filtered by campus ID (caid)
    $teachers = Subjects::where('trtype', $type)->get();
    
    return response()->json([
        'data' => $teachers,
    ]);
}
    public function destroy($id)
{
    try {
        $teachers = Subjects::findOrFail($id);
        $teachers->delete();
        
        return response()->json([
            'message' => 'Teacher deleted successfully'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}


public function update(Request $request, $id) {
    try {
        Log::info('Update request data:', $request->all());
        Log::info('Teacher ID:', ['id' => $id]);
        
        $validator = Validator::make($request->all(), [
            'surname' => 'required|string|max:255',
            'fname' => 'required|string|max:255',
            'workno' => 'required|string|unique:tblteachers,workno,'.$id.',ID', // Specify the primary key column
            'gender' => 'required|string|in:male,female',
            'trtype' => 'required|string|in:normal,HOD',
            'phoneno' => 'required|string|max:20',
            'dateemployed' => 'nullable|date',
            'email' => 'required|email|unique:tblteachers,email,'.$id.',ID', // Specify the primary key column
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $teacher = Subjects::findOrFail($id);
        Log::info('Teacher before update:', $teacher->toArray());
        
        $validatedData = $validator->validated();
        Log::info('Validated data:', $validatedData);
        
        $updated = $teacher->update($validatedData);
        Log::info('Update result:', ['updated' => $updated]);
        
        // Reload the model to get fresh data
        $teacher->refresh(); // Use refresh() instead of fresh()
        Log::info('Teacher after update:', $teacher->toArray());

        return response()->json([
            'message' => 'Teacher updated successfully'
        ], 200);
        
    } catch (\Exception $e) {
        Log::error('Teacher update error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        return response()->json([
            'error' => 'Server error occurred: ' . $e->getMessage()
        ], 500);
    }
}
}