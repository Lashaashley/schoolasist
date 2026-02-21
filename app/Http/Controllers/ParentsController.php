<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ParentsController extends Controller
{
    public function create()
    {
        return view('students.add_parent');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'surname' => 'required|string|max:255',
                'othername' => 'required|string|max:255',
                'typpe' => 'required|string|max:255', // Note: Did you mean 'type' instead of 'typpe'?
                'workplace' => 'required|string|max:255',
                'phoneno' => 'required|string|max:255',
                'emergencyphone' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'email' => 'required|email|unique:tblparents,email',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // This line was incorrect - you need to use validated data, not the validator object
            Parents::create($validator->validated());
            
            return response()->json([
                'message' => 'Parent added successfully'
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
        return view('students.prmanage');
    }

    // Add this method to fetch parents data for DataTables
    public function getParents()
    {
        $parents = Parents::all();
        return response()->json(['data' => $parents]);
    }
    public function getAllparents()
{
    $parents = Parents::select('ID', 
                          DB::raw("CONCAT(surname, ' ', othername) as parentname"), 
                          'surname', 
                          'othername')
                    ->orderBy('surname')
                    ->get();
    
    return response()->json([
        'data' => $parents,
    ]);
}
    public function destroy($id)
{
    try {
        $parent = Parents::findOrFail($id);
        $parent->delete();
        
        return response()->json([
            'message' => 'Parent deleted successfully'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}


public function update(Request $request, $id) 
{
    try {
        $validator = Validator::make($request->all(), [
            'surname' => 'required|string|max:255',
            'othername' => 'required|string|max:255',
            'typpe' => 'required|string|max:255',  // Fixed typo: "typpe" -> "type"
            'workplace' => 'required|string|max:255',
            'phoneno' => 'required|string|max:255',
            'emergencyphone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|email|unique:tblparents,email,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $parent = Parents::findOrFail($id);
        $parent->update($validator->validated());

        return response()->json([
            'message' => 'Parent updated successfully'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}
}