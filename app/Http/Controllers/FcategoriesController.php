<?php

namespace App\Http\Controllers;

use App\Models\Fcategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class FcategoriesController extends Controller
{
    public function create()
{
    $fcategories = Fcategories::distinct()->get(['ID', 'branchname']);
    dd($fcategories); // Debug data
    return view('students.static', compact('fcategories'));
}


    public function store(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'catename' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    // Insert into the database
    Fcategories::create([
        'catename' => $request->catename,
    ]);

    return response()->json([
        'message' => 'Fee Category Saved!',
    ]);
}

public function getAll()
{
    $fcategories = Fcategories::paginate(3); // 3 records per page

    return response()->json([
        'data' => $fcategories->items(),
        'pagination' => [
            'current_page' => $fcategories->currentPage(),
            'last_page' => $fcategories->lastPage(),
            'per_page' => $fcategories->perPage(),
            'total' => $fcategories->total(),

        ],
    ]);
}

public function getAllCategories()
{
    // Fetch all branches
    $fcategories = Fcategories::all();

    return response()->json([
        'data' => $fcategories,
    ]);
}




public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $fcategories = Fcategories::findOrFail($id);
    
    $data = $request->validate([
        'branchname' => 'required|string|max:255',
    ]);

    
    Log::info('Validated data:', $data); // Add logging for debugging
    
    $fcategories->update($data);
    
    Log::info('After update:', $fcategories->toArray()); // Add logging for debugging

    return response()->json([
        'message' => 'Branch updated successfully',
        'data' => $fcategories
    ]);
}

public function destroy($id)
{
    $fcategories = Fcategories::find($id);

    if (!$fcategories) {
        return response()->json([
            'success' => false,
            'message' => 'Category not found.'
        ], 404);
    }

    try {
        $fcategories->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully!'
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

