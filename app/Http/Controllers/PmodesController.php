<?php

namespace App\Http\Controllers;

use App\Models\Pmodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PmodesController extends Controller
{
    public function create()
{
    $pmodes = Pmodes::distinct()->get(['ID', 'pname', 'sstatus', 'tcode', 'chequeno', 'bankn']);
    dd($pmodes); // Debug data
    return view('students.static', compact('pmodes'));
}


    public function store(Request $request)
{
    $request->merge([
        'tcode' => $request->has('tcode') ? $request->tcode : 'No',
        'chequeno' => $request->has('chequeno') ? $request->chequeno : 'No',
        'bankn' => $request->has('bankn') ? $request->bankn : 'No',
    ]);

    // Then validate
    $validator = Validator::make($request->all(), [
        'pname' => 'required|string|max:255',
        'sstatus' => 'required|string|max:255',
        'tcode' => 'required|string|max:255',
        'chequeno' => 'required|string|max:255',
        'bankn' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    Pmodes::create($request->only(['pname', 'sstatus', 'tcode', 'chequeno', 'bankn']));

    return response()->json([
        'message' => 'Pay mode Saved!',
    ]);
}


public function getAll()
{
    $pmodes = Pmodes::paginate(3); // 3 records per page

    return response()->json([
        'data' => $pmodes->items(),
        'pagination' => [
            'current_page' => $pmodes->currentPage(),
            'last_page' => $pmodes->lastPage(),
            'per_page' => $pmodes->perPage(),
            'total' => $pmodes->total(),

        ],
    ]);
}

public function getAllCategories()
{
    // Fetch all branches
    $pmodes = Pmodes::all();

    return response()->json([
        'data' => $pmodes,
    ]);
}

public function getrequired(Request $request) {
    $selectedmethod = $request->input('pmethod');
    
    // Fetch payment mode details by ID
    $pmodes = Pmodes::where('ID', $selectedmethod)->get();
    
    return response()->json([
        'data' => $pmodes,
    ]);
}



public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $pmodes = Pmodes::findOrFail($id);
    
    $data = $request->validate([
        'pname' => 'required|string|max:255',
        'sstatus' => 'required|string|max:255',
        'tcode' => 'nullable|string|max:255',
        'chequeno' => 'nullable|string|max:255',
        'bankn' => 'nullable|string|max:255',
    ]);

    
    Log::info('Validated data:', $data); // Add logging for debugging
    
    $pmodes->update($data);
    
    Log::info('After update:', $pmodes->toArray()); // Add logging for debugging

    return response()->json([
        'message' => 'Paymode updated successfully',
        'data' => $pmodes
    ]);
}

public function destroy($id)
{
    $pmodes = Pmodes::find($id);

    if (!$pmodes) {
        return response()->json([
            'success' => false,
            'message' => 'Pay mode not found.'
        ], 404);
    }

    try {
        $pmodes->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pay mode deleted successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete Category. Please try again.',
            'error' => $e->getMessage()
        ], 500);
    }
}



}

