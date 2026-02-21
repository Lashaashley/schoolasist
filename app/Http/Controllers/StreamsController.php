<?php

namespace App\Http\Controllers;

use App\Models\Streams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class StreamsController extends Controller
{
    public function create()
{
    $streams = Streams::distinct()->get(['ID', 'strmname']);
    dd($streams); // Debug data
    return view('students.static', compact('streams'));
}


public function store(Request $request) 
{
    // Log the incoming request data
    Log::info('Incoming request data:', $request->all());

    // Validate the request
    $validator = Validator::make($request->all(), [
        'strmname' => 'required|string|max:255|unique:streams,strmname',
        'streamclass' => 'required|string|max:255',  // Changed from 'classid'
        'streamteach' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    // Insert into the database
    Streams::create([
        'strmname' => $request->strmname,
        'classid' => $request->streamclass,
        'classteach' => $request->streamteach,
    ]);

    return response()->json([
        'message' => 'Stream Saved!',
    ]);
}



public function getAll()
{
    $streams = Streams::paginate(3); // 3 records per page

    return response()->json([
        'data' => $streams->items(),
        'pagination' => [
            'current_page' => $streams->currentPage(),
            'last_page' => $streams->lastPage(),
            'per_page' => $streams->perPage(),
            'total' => $streams->total(),

        ],
    ]);
}

public function getAllStreams()
{
    // Fetch all branches
    $streams = Streams::all();

    return response()->json([
        'data' => $streams,
    ]);
}


public function getstreamByClass(Request $request) {
    $campusId = $request->input('campusId');
    
    // Fetch classes filtered by campus ID (caid)
    $streams = Streams::where('classid', $campusId)->get();
    
    return response()->json([
        'data' => $streams,
    ]);
}

public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $streams = Streams::findOrFail($id);
    
    $data = $request->validate([
        'strmname' => 'required|string|max:255|unique:streams,strmname',
    ]);

    
    Log::info('Validated data:', $data); // Add logging for debugging
    
    $streams->update($data);
    
    Log::info('After update:', $streams->toArray()); // Add logging for debugging

    return response()->json([
        'message' => 'Stream updated successfully',
        'data' => $streams
    ]);
}

public function destroy($id)
{
    $streams = Streams::find($id);

    if (!$streams) {
        return response()->json([
            'success' => false,
            'message' => 'Stream not found.'
        ], 404);
    }

    try {
        $streams->delete();

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

