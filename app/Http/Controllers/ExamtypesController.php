<?php

namespace App\Http\Controllers;

use App\Models\Examtypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ExamtypesController extends Controller
{
    public function create()
    {
        $examtypes = Examtypes::distinct()->get(['ID', 'examname']);
        
        // Comment out the dd() for production
      //dd($feeite); // Debug data
        
        return view('students.examtypes', compact('examtypes'));
    }
    
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'examname' => 'required|string|max:255|unique:examtypes,examname',
            
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        
        // Prepare data array
        $data = [
            'examname' => $request->examname,
        ];
        
        // Insert into the database
        Examtypes::create($data);
        
        return response()->json([
            'message' => 'Exam Type Saved!',
        ]);
    }

    public function getAll()
{
    $examtypes = Examtypes::paginate(3); // 3 records per page

    return response()->json([
        'data' => $examtypes->items(),
        'pagination' => [
            'current_page' => $examtypes->currentPage(),
            'last_page' => $examtypes->lastPage(),
            'per_page' => $examtypes->perPage(),
            'total' => $examtypes->total(),

        ],
    ]);
}


public function getExams()
{
    // Fetch all branches
    $examtypes = Examtypes::all();

    return response()->json([
        'data' => $examtypes,
    ]);
}




public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $feeite = Examtypes::findOrFail($id);
    
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
    $feeite = Examtypes::find($id);

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

