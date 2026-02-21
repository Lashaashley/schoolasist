<?php

namespace App\Http\Controllers; 

use App\Models\Feeassign;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FeeassignController extends Controller
{
    public function create()
    {
        $assigned = Feeassign::distinct()->get(['ID', 'feeid', 'feeamount','classid']);
        
        // Comment out the dd() for production
      //dd($feeite); // Debug data
        
        return view('students.assign', compact('assigned'));
    }
    public function modify()
    {
        return view('students.assignmodify');
    }
    
    public function store(Request $request)
{
    // Validate the request
    $request->validate([
        'classid' => 'required|exists:tblclasses,ID',
        'feeItems' => 'required|array',
        'feeItems.*' => 'exists:feeitems,ID',
    ]);
    
    try {
        DB::beginTransaction();
        
        // Get the active period
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->value('ID');
        
        if (!$activePeriod) {
            return response()->json([
                'success' => false,
                'message' => 'No active period found. Please create an active period first.'
            ], 422);
        }
        
        // Delete existing fee assignments for this class
        Feeassign::where('classid', $request->classid)->delete();
        
        // Get all students in this class
        $students = Student::where('claid', $request->classid)->get();
        
        if ($students->isEmpty()) {
            // If no students, just create the fee assignments
            foreach ($request->feeItems as $feeId) {
                $fee = DB::table('feeitems')->where('ID', $feeId)->first();
                $feeAmount = $fee ? $fee->amount : 0;
                
                Feeassign::create([
                    'feeid' => $feeId,
                    'classid' => $request->classid,
                    'feeamount' => $feeAmount,
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Fee items assigned successfully. No students in this class yet.'
            ]);
        }
        
        // Create new fee assignments and update managefee for each student
        foreach ($request->feeItems as $feeId) {
            // Get fee amount from the feeitems table
            $fee = DB::table('feeitems')->where('ID', $feeId)->first();
            $feeAmount = $fee ? $fee->amount : 0;
            
            // Create fee assignment
            Feeassign::create([
                'feeid' => $feeId,
                'classid' => $request->classid,
                'feeamount' => $feeAmount,
            ]);
            
            // Update/Insert managefee for each student in this class
            foreach ($students as $student) {
                // Check if this fee already exists for this student in the current period
                $existingFee = DB::table('managefee')
                    ->where('admno', $student->admno)
                    ->where('feeid', $feeId)
                    ->where('period', $activePeriod)
                    ->first();
                
                if ($existingFee) {
                    // Update existing fee record
                    // Only update if no payment has been made
                    if ($existingFee->paid == 0) {
                        DB::table('managefee')
                            ->where('admno', $student->admno)
                            ->where('feeid', $feeId)
                            ->where('period', $activePeriod)
                            ->update([
                                'classid' => $request->classid,
                                'amount' => $feeAmount,
                                'balance' => $feeAmount,
                                'updated_at' => now()
                            ]);
                    } else {
                        // If payment has been made, recalculate balance
                        $newBalance = $feeAmount - $existingFee->paid;
                        
                        DB::table('managefee')
                            ->where('admno', $student->admno)
                            ->where('feeid', $feeId)
                            ->where('period', $activePeriod)
                            ->update([
                                'classid' => $request->classid,
                                'amount' => $feeAmount,
                                'balance' => $newBalance,
                                'status' => $newBalance > 0 ? 'Partial' : 'Paid',
                                'updated_at' => now()
                            ]);
                    }
                } else {
                    // Insert new fee record
                    DB::table('managefee')->insert([
                        'admno' => $student->admno,
                        'classid' => $request->classid,
                        'feeid' => $feeId,
                        'amount' => $feeAmount,
                        'paid' => 0,
                        'balance' => $feeAmount,
                        'status' => 'Pending',
                        'period' => $activePeriod,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                
                // Handle boarding fees if student is a boarder
                if ($student->border === 'yes' && $student->houseid) {
                    // Check if the assigned fee is a general fee (not house-specific)
                    // and if there are house-specific fees that need to be added
                    $isGeneralFee = DB::table('feeitems')
                        ->where('ID', $feeId)
                        ->whereNull('house')
                        ->exists();
                    
                    if ($isGeneralFee) {
                        // Get boarding fees for this student's house
                        $boardingFees = DB::table('feeitems')
                            ->where('house', $student->houseid)
                            ->get();
                        
                        foreach ($boardingFees as $boardingFee) {
                            // Check if boarding fee already exists
                            $existingBoardingFee = DB::table('managefee')
                                ->where('admno', $student->admno)
                                ->where('feeid', $boardingFee->ID)
                                ->where('period', $activePeriod)
                                ->first();
                            
                            if (!$existingBoardingFee) {
                                // Insert boarding fee
                                DB::table('managefee')->insert([
                                    'admno' => $student->admno,
                                    'classid' => $request->classid,
                                    'feeid' => $boardingFee->ID,
                                    'amount' => $boardingFee->amount,
                                    'paid' => 0,
                                    'balance' => $boardingFee->amount,
                                    'status' => 'Pending',
                                    'period' => $activePeriod,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                            }
                        }
                    }
                }
            }
        }
        
        // Remove fees from managefee that are no longer assigned to this class
        $assignedFeeIds = $request->feeItems;
        
        // Get general fees (non-house specific) that should be removed
        $generalFeesToRemove = DB::table('feeitems')
            ->whereNotIn('ID', $assignedFeeIds)
            ->whereNull('house')
            ->pluck('ID')
            ->toArray();
        
        if (!empty($generalFeesToRemove)) {
            foreach ($students as $student) {
                // Only delete if no payment has been made
                DB::table('managefee')
                    ->where('admno', $student->admno)
                    ->where('classid', $request->classid)
                    ->where('period', $activePeriod)
                    ->whereIn('feeid', $generalFeesToRemove)
                    ->where('paid', 0)
                    ->delete();
            }
        }
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Fee items assigned successfully and student fees updated for ' . $students->count() . ' student(s)'
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        
        Log::error('Fee assignment error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to assign fee items: ' . $e->getMessage()
        ], 500);
    }
}

public function getAll()
{
    $feeite = Feeassign::paginate(8); // 3 records per page

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
    $feeitems = Feeassign::all();

    return response()->json([
        'data' => $feeitems,
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
        $assignments = Feeassign::where('classid', $request->classid)->get();
        
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


public function update(Request $request, $id)
{
    Log::info('Update request data:', $request->all()); // Add logging for debugging
    
    $feeite = Feeassign::findOrFail($id);
    
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
    $feeite = Feeassign::find($id);

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

