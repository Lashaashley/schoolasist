<?php

namespace App\Http\Controllers;

use App\Models\Houses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class HousesController extends Controller
{
    public function create()
{
    $houses = Houses::distinct()->get(['ID', 'brid', 'housen']);
    dd($houses); // Debug data
    return view('students.static', compact('houses'));
}


    public function store(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'branch2' => 'required|string|max:255',
        'housename' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    // Insert into the database
    Houses::create([
        'brid' => $request->branch2,
        'housen' => $request->housename,
    ]);

    return response()->json([
        'message' => 'House Saved!',
    ]);
}

public function getAll()
{
    // Join houses with branches to include branchname
    $houses = Houses::join('branches', 'houses.brid', '=', 'branches.ID')
        ->select('houses.*', 'branches.branchname') // Select desired columns
        ->paginate(3); // Paginate the results

    return response()->json([
        'data' => $houses->items(),
        'pagination' => [
            'current_page' => $houses->currentPage(),
            'last_page' => $houses->lastPage(),
            'per_page' => $houses->perPage(),
            'total' => $houses->total(),
        ],
    ]);
}

public function getAllHouses()
{
    // Fetch all branches
    $houses = Houses::all();

    return response()->json([
        'data' => $houses,
    ]);
}
public function gethousesByCampus(Request $request) {
    $campusId = $request->input('campusId');
    
    // Fetch classes filtered by campus ID (caid)
    $houses = Houses::where('brid', $campusId)->get();
    
    return response()->json([
        'data' => $houses,
    ]);
}



public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $houses = Houses::findOrFail($id);
    
    $data = $request->validate([
        'brid' => 'required|string|max:255',
        'housen' => 'required|string|max:255',
    ]);

    
    Log::info('Validated data:', $data); // Add logging for debugging
    
    $houses->update($data);
    
    Log::info('After update:', $houses->toArray()); // Add logging for debugging

    return response()->json([
        'message' => 'House updated successfully',
        'data' => $houses
    ]);
}

public function destroy($id)
{
    $house = Houses::find($id);

    if (!$house) {
        return response()->json([
            'success' => false,
            'message' => 'Branch not found.'
        ], 404);
    }

    try {
        $house->delete();

        return response()->json([
            'success' => true,
            'message' => 'House deleted successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete house. Please try again.',
            'error' => $e->getMessage()
        ], 500);
    }
}



}

