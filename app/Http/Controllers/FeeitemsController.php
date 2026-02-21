<?php

namespace App\Http\Controllers;

use App\Models\Feeitems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class FeeitemsController extends Controller
{
    public function create()
    {
        $feeitems = Feeitems::distinct()->get(['ID', 'feename','amount']);
        
        // Comment out the dd() for production
      //dd($feeite); // Debug data
        
        return view('students.feeitems', compact('feeitems'));
    }
    
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'feename' => 'required|string|max:255|unique:feeitems,feename',
            'category' => 'required|string|max:255',
            'house' => 'nullable|string|max:255', // Changed to nullable
            'amount' => [
                'required',
                'regex:/^\d+(\.\d{1,2})?$/', // Allows up to 2 decimal places
            ],
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        
        // Prepare data array
        $data = [
            'feename' => $request->feename,
            'amount' => $request->amount,
            'category' => $request->category,
        ];
        
        // Only add house to the data if category is 4 and house is provided
        if ($request->category == '4' && $request->filled('house')) {
            $data['house'] = $request->house;
        } else {
            $data['house'] = null; // Explicitly set as null for other categories
        }
        
        // Insert into the database
        Feeitems::create($data);
        
        return response()->json([
            'message' => 'Fee Item Saved!',
        ]);
    }


    public function getAll() {
        try {
            $feeite = Feeitems::join('fcategories', 'feeitems.category', '=', 'fcategories.ID')
        ->leftJoin('houses', 'feeitems.house', '=', 'houses.ID')
        ->select('feeitems.*', 'fcategories.catename', 'houses.housen')
        ->paginate(8);
            
            return response()->json([
                'data' => $feeite->items(),
                'pagination' => [
                    'current_page' => $feeite->currentPage(),
                    'last_page' => $feeite->lastPage(),
                    'per_page' => $feeite->perPage(),
                    'total' => $feeite->total(),
                ],
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in getAll method: ' . $e->getMessage());
            
            // Return a helpful error response
            return response()->json([
                'error' => 'An error occurred while fetching data',
                'message' => $e->getMessage()
            ], 500);
        }
    }


public function getAllFeeitems()
{
    // Fetch all branches
    $feeitems = Feeitems::all();

    return response()->json([
        'data' => $feeitems,
    ]);
}




public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $feeite = Feeitems::findOrFail($id);
    
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
    $feeite = Feeitems::find($id);

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

