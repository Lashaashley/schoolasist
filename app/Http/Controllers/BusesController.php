<?php

namespace App\Http\Controllers;

use App\Models\Buses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BusesController extends Controller
{
    public function create()
    {
        $buses = Buses::distinct()->get(['ID', 'busna']);
        
        // Comment out the dd() for production
        // dd($buses); // Debug data
        
        return view('students.set', compact('buses'));
    }
    
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'busna' => 'required|string|max:255|unique:buses,busna',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        
        // Insert into the database
        Buses::create([
            'busna' => $request->busna,
        ]);
        
        return response()->json([
            'message' => 'Vehicle Saved!',
        ]);
    }

public function getAll()
{
    $buses = Buses::paginate(3); // 3 records per page

    return response()->json([
        'data' => $buses->items(),
        'pagination' => [
            'current_page' => $buses->currentPage(),
            'last_page' => $buses->lastPage(),
            'per_page' => $buses->perPage(),
            'total' => $buses->total(),

        ],
    ]);
}

public function getAllBuses()
{
    // Fetch all branches
    $buses = Buses::all();

    return response()->json([
        'data' => $buses,
    ]);
}




public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $buses = Buses::findOrFail($id);
    
    $data = $request->validate([
        'busna' => 'required|string|max:255|unique:buses,busna',
    ]);

    
    Log::info('Validated data:', $data); // Add logging for debugging
    
    $buses->update($data);
    
    Log::info('After update:', $buses->toArray()); // Add logging for debugging

    return response()->json([
        'message' => 'Branch updated successfully',
        'data' => $buses
    ]);
}

public function destroy($id)
{
    $buses = Buses::find($id);

    if (!$buses) {
        return response()->json([
            'success' => false,
            'message' => 'Vehicle not found.'
        ], 404);
    }

    try {
        $buses->delete();

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

