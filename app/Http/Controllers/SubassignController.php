<?php

namespace App\Http\Controllers;

use App\Models\Subassign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SubassignController extends Controller
{
    public function create()
    {
        $assigned = Subassign::distinct()->get(['ID', 'subid', 'classid','classtr']);
        
        // Comment out the dd() for production
      //dd($feeite); // Debug data
        
        return view('students.assignsub', compact('assigned'));
    }
    public function modify()
    {
        return view('students.assignmodify');
    }
    
  public function store(Request $request)
{
    // ✅ Validate request
    $request->validate([
        'classid'                => 'required|exists:streams,ID',
        'subjects'               => 'required|array',
        'subjects.*.subid'       => 'required|exists:tblsubjects,ID',
        'subjects.*.teacherid'   => 'required|exists:tblteachers,ID',
    ]);

    try {
        DB::beginTransaction();

        // ✅ Delete old assignments for this class
        Subassign::where('classid', $request->classid)->delete();

        // ✅ Prepare new records for bulk insert
        $insertData = collect($request->subjects)->map(function ($subject) use ($request) {
    return [
        'subid'      => $subject['subid'],
        'classid'    => $request->classid,
        'classtr'    => $subject['teacherid'],
        'created_at' => now(),
        'updated_at' => now(),
    ];
})->toArray();




        // ✅ Insert all at once (faster than looping create())
        if (!empty($insertData)) {
            Subassign::insert($insertData);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Subjects assigned successfully',
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Failed to assign subjects: ' . $e->getMessage(),
        ], 500);
    }
}

public function getSubjectStudents($subjectId, Request $request)
{
    $classid = $request->query('classid'); // Get ?classid=... from URL

    // Get students already assigned
    $assigned = DB::table('tblsubassign')
        ->join('students', 'students.admno', '=', 'tblsubassign.stadmno')
        ->join('streams', 'streams.ID', '=', 'students.stream')
        ->select('students.admno', 'students.sirname', 'students.othername', 'streams.strmname')
        ->where('tblsubassign.subid', $subjectId)
        ->where('students.claid', $classid)
        ->get();

    // Get students not yet assigned
    $assignedIds = $assigned->pluck('admno');
    $available = DB::table('students')
        ->join('streams', 'streams.ID', '=', 'students.stream')
        ->select('students.admno', 'students.sirname', 'students.othername', 'streams.strmname')
        ->where('students.claid', $classid)
        ->whereNotIn('students.admno', $assignedIds)
        ->get();

    return response()->json([
        'available' => $available,
        'assigned'  => $assigned
    ]);
}


// FIXED Controller Function:
public function saveSubjectStudents(Request $request, $subjectId)
{
    $request->validate([
        'students' => 'array'
    ]);

    DB::beginTransaction();
    try {
        $teacherId = $request->input('trid');
        $classid = $request->input('classid');

        // Remove old assignments
        DB::table('tblsubassign')
            ->where('subid', $subjectId)
            ->whereNotNull('stadmno')
            ->delete();

        // Insert new assignments
        $insertData = collect($request->students)->map(function ($admno) use ($subjectId, $teacherId, $classid) {
            return [
                'subid'      => $subjectId,
                'stadmno'    => $admno,
                'classtr'    => $teacherId,
                'classident' => $classid,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        if (!empty($insertData)) {
            DB::table('tblsubassign')->insert($insertData);
        }

        DB::commit();
        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}



public function getAll()
{
    $feeite = Subassign::paginate(8); // 3 records per page

    return response()->json([
        'data' => $feeite->items(),
        'pagination' => [
            'current_page' => $feeite->currentPage(),
            'last_page' => $feeite->lastPage(),
            'per_page' => $feeite->perPage(),
            'total' => $feeite->total(),

        ],
    ]);
}

public function getAllFeeitems()
{
    // Fetch all branches
    $subjects = Subassign::all();

    return response()->json([
        'data' => $subjects,
    ]);
}



public function getAssignments(Request $request)
{
    $validator = Validator::make($request->all(), [
        'classid' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Class ID is required'
        ], 400);
    }

    try {
        $assignments = Subassign::where('classid', $request->classid)->get();
        
        return response()->json([
            'success' => true,
            'data' => $assignments
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to get assignments: ' . $e->getMessage()
        ], 500);
    }
}

public function getclassbysub(Request $request)
{
    try {
        $subjectId = $request->input('campusId');

        $classes = Subassign::leftJoin('streams', 'tblsubassign.classid', '=', 'streams.ID')
            ->leftJoin('tblclasses', 'tblsubassign.classident', '=', 'tblclasses.ID')
            ->select(
                DB::raw('COALESCE(streams.ID, tblclasses.ID) as entity_id'),
                DB::raw('COALESCE(streams.strmname, tblclasses.claname) as name'),
                DB::raw('CASE WHEN streams.ID IS NOT NULL THEN "stream" ELSE "class" END as type')
            )
            ->where('tblsubassign.subid', $subjectId)
            ->distinct()
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $classes]);
    } catch (\Exception $e) {
        Log::error('Error in getclassbysub: ' . $e->getMessage());
        return response()->json([
            'error' => 'An error occurred while fetching data',
            'message' => $e->getMessage()
        ], 500);
    }
}





public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $feeite = Subassign::findOrFail($id);
    
    $data = $request->validate([
        'feename' => 'required|string|max:255',
            'amount' => [
                'required',
                'regex:/^\d+(\.\d{1,2})?$/', // Allows up to 2 decimal places
            ],
    ]);

    
    Log::info('Validated data:', $data); // Add logging for debugging
    
    $feeite->update($data);
    
    Log::info('After update:', $feeite->toArray()); // Add logging for debugging

    return response()->json([
        'message' => 'Feeitem updated successfully',
        'data' => $feeite
    ]);
}

public function destroy($id)
{
    $feeite = Subassign::find($id);

    if (!$feeite) {
        return response()->json([
            'success' => false,
            'message' => 'Fee Item not found.'
        ], 404);
    }

    try {
        $feeite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle deleted successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete Vehicle. Please try again.',
            'error' => $e->getMessage()
        ], 500);
    }
}



}

