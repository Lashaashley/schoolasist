<?php

namespace App\Http\Controllers;

use App\Models\Teachers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TeachersController extends Controller
{
    public function create()
    {
        return view('students.add_teacher');
    }

    public function store(Request $request)
    {
        try { 
            $validator = Validator::make($request->all(), [
                'surname' => 'required|string|max:255',
                'fname' => 'required|string|max:255',
                'workno' => 'required|string|unique:tblteachers,workno', // Note: Did you mean 'type' instead of 'typpe'?
                'gender' => 'required|string|max:255',
                'trtype' => 'required|string|max:255',
                'phoneno' => 'required|string|max:255',
                'dateemployed' => 'nullable|string|max:255',
                'profile' => 'nullable|image|max:2048',
                'email' => 'required|email|unique:tblteachers,email',
            ]); 
            
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            $path = null;
            if ($request->hasFile('profile')) {
                $path = $request->file('profile')->store('profile-photos', 'public');
                /*Then when displaying:

php
Copy
Edit
<img src="{{ asset('storage/' . $teacher->profile) }}">*/

            }
            // This line was incorrect - you need to use validated data, not the validator object
            Teachers::create(array_merge(
                $validator->validated(),
                ['profile' => $path]
            ));

            
            return response()->json([
                'message' => 'Teacher added successfully'
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
        return view('students.trmanage');
    }

    // Add this method to fetch parents data for DataTables
    public function getteachers()
    {
        $teachers = Teachers::all();
        return response()->json(['data' => $teachers]);
    }
    public function getAllteachers()
{
    $teachers = Teachers::select('ID', 
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
    $teachers = Teachers::where('trtype', $type)->get();
    
    return response()->json([
        'data' => $teachers,
    ]);
}
    public function destroy($id)
{
    try {
        $teachers = Teachers::findOrFail($id);
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

        $teacher = Teachers::findOrFail($id);
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