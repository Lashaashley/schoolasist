<?php

namespace App\Http\Controllers;
 
use App\Models\Grades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class GradingController extends Controller
{
    public function create()
{
    $Grades = Grades::distinct()->get(['ID', 'Grade', 'Min', 'Max','Remarks']);
    dd($Grades); // Debug data
    return view('students.examtypes', compact('grading'));
}




public function store(Request $request) 
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'Grade' => 'required|string|max:255|unique:grading,Grade',
        'Min' => 'required|string|max:255',  // Changed from 'classid'
        'Max' => 'required|string|max:255',
        'Remarks' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    // Insert into the database
    Grades::create([
        'Grade' => $request->Grade,
        'Min' => $request->Min,
        'Max' => $request->Max,
        'Remarks' => $request->Remarks,
    ]);

    return response()->json([
        'message' => 'Grade Saved!',
    ]);
}




public function getAll()
{
// 3 records per page
    $classes = Grades::join('streams', 'tblclasses.stid', '=', 'streams.ID')
        ->select('tblclasses.*', 'streams.strmname') // Select desired columns
        ->paginate(3); // Paginate the results

    return response()->json([
        'data' => $classes->items(),
        'pagination' => [
            'current_page' => $classes->currentPage(),
            'last_page' => $classes->lastPage(),
            'per_page' => $classes->perPage(),
            'total' => $classes->total(),

        ],
    ]);
}

public function getAllGrades()
{
    // Fetch all branches
    $classes = Grades::all();

    return response()->json([
        'data' => $classes,
    ]);
}
// In your GradesController
public function getGradesByCampus(Request $request) {
    $campusId = $request->input('campusId');
    
    // Fetch classes filtered by campus ID (caid)
    $classes = Grades::where('caid', $campusId)->get();
    
    return response()->json([
        'data' => $classes,
    ]);
}
public function getAllGrades2() {
    // Fetch all classes that DO NOT exist in the feeassign table
    $classes = DB::table('tblclasses')
        ->leftJoin('feeassign', 'tblclasses.ID', '=', 'feeassign.classid')
        ->whereNull('feeassign.classid')  // Only include rows where feeassign.classid is null
        ->select('tblclasses.ID', 'tblclasses.claname')
        ->distinct()
        ->get();
    
    return response()->json([
        'data' => $classes,
    ]);
}

public function getAllGrades3() {
    $classes = DB::table('tblclasses')
                ->join('feeassign', 'tblclasses.ID', '=', 'feeassign.classid')
                ->select('tblclasses.ID', 'tblclasses.claname')
                ->distinct()
                ->get();
    
    return response()->json([
        'data' => $classes,
    ]);
}

public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $classes = Grades::findOrFail($id);
    
    $data = $request->validate([
        'stid' => 'required|string|max:255',
        'claname' => [
            'required', 
            'string', 
            'max:255',
            Rule::unique('tblclasses')->where(function ($query) use ($request) {
                return $query->where('stid', $request->stid)
                             ->where('claname', $request->claname)
                             ->where('clateach', $request->clateach)
                             ->where('clarank', $request->clarank);
            }),
        ],
        'clarank' => 'required|string|max:255',
        'clateach' => 'required|string|max:255',
    ]);

    
    Log::info('Validated data:', $data); // Add logging for debugging
    
    $classes->update($data);
    
    Log::info('After update:', $classes->toArray()); // Add logging for debugging

    return response()->json([
        'message' => 'Class updated successfully',
        'data' => $classes
    ]);
}

public function destroy($id)
{
    $classes = Grades::find($id);

    if (!$classes) {
        return response()->json([
            'success' => false,
            'message' => 'Stream not found.'
        ], 404);
    }

    try {
        $classes->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stream deleted successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete branch. Please try again.',
            'error' => $e->getMessage()
        ], 500);
    }
}



}

