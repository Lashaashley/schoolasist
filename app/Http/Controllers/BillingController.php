<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Feeassign;
use App\Models\Managefee;
use App\Models\Receipts;
use App\Models\Feeitems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Structure;

class BillingController extends Controller {
    public function create()
    {
        return view('students.billing');
    }

     public function index()
    {
        return view('students.fee_reports');
    }

    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'admno' => 'required|string|max:255',
                'classid' => 'required|string|max:255',
                'feeid' => 'required|string|max:255',
                'amount' => 'required|string|max:255',
                'paid' => 'required|string|max:255',
                'balance' => 'required|string|max:255',
                'status' => 'required|string|max:255',
                'period' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create the student record
            $managefee = Managefee::create([
                'admno' => $request->admno,
                'classid' => $request->sirname,
                'feeid' => $request->othername,
                'amount' => $request->gender,
                'paid' => $request->dateob,
                'balance' => $request->admdate,
                'status' => $request->caid,
                'period' => $request->claid,
                
                
            ]);

            
           

            return response()->json([
                'message' => 'Student added successfully with fee assignments!'
            ]);

        } catch (\Exception $e) {
            // Enhanced error logging
            Log::error('Student creation error: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            
            return response()->json([
                'error' => 'Failed to add student: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getstudents(Request $request)
{
    try {
        $classId = $request->input('selectedclassId'); // Changed from campus_id to selectedclassId
        
        if (!$classId) {
            return response()->json([
                'error' => 'Class ID is required'
            ], 400);
        }
        
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->value('ID');
        if (!$activePeriod) {
            return response()->json([
                'errors' => ['general' => ['No active period found']]
            ], 422);
        }
        
        $students = DB::table('managefee')
            ->select(
                'managefee.admno', 
                DB::raw('CONCAT(students.sirname, " ", students.othername) as studentname')
            )
            ->join('students', 'managefee.admno', '=', 'students.admno')
            ->where('managefee.classid', $classId)
            ->where('managefee.period', $activePeriod)
            ->distinct()
            ->get();
        
        return response()->json($students); // Return the students directly
        
    } catch (\Exception $e) {
        Log::error('Error fetching students : ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        return response()->json([
            'error' => 'Failed to fetch students : ' . $e->getMessage()
        ], 500);
    }
}

  public function getstudents2(Request $request)
{
    try {
        $classId = $request->input('selectedclassId');
        
        if (!$classId) {
            return response()->json([
                'error' => 'Class ID is required'
            ], 400);
        }
        
        $students = DB::table('students')
            ->select(
                'admno', 
                DB::raw('CONCAT(sirname, " ", othername) as studentname')
            )
            ->where('claid', $classId)
            ->orderBy('sirname')
            ->get();
        
        return response()->json($students);
        
    } catch (\Exception $e) {
        Log::error('Error fetching students: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        return response()->json([
            'error' => 'Failed to fetch students: ' . $e->getMessage()
        ], 500);
    }
}

public function getStudentBillingDetails(Request $request) {
    try {
        $admno = $request->input('admno');
        
        if (!$admno) {
            return response()->json([
                'error' => 'Student admission number is required'
            ], 400);
        }
        
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->value('ID');
        if (!$activePeriod) {
            return response()->json([
                'errors' => ['general' => ['No active period found']]
            ], 422);
        }
        
        // Get student name
        $student = DB::table('students')
            ->select(DB::raw('CONCAT(sirname, " ", othername) as studentname'))
            ->where('admno', $admno)
            ->first();
            
        if (!$student) {
            return response()->json([
                'error' => 'Student not found'
            ], 404);
        }
        
        // Get billing details with fee names
        $billingDetails = DB::table('managefee')
            ->select(
                'managefee.ID',
                'managefee.feeid',
                'feeitems.feename',
                'managefee.amount',
                'managefee.paid',
                'managefee.balance',
                'managefee.status',
                'managefee.created_at as date_posted'
            )
            ->join('feeitems', 'managefee.feeid', '=', 'feeitems.ID')
            ->where('managefee.admno', $admno)
            ->where('managefee.period', $activePeriod)
            ->get();
            
        // Calculate totals
        $totals = [
            'total_amount' => $billingDetails->sum('amount'),
            'total_paid' => $billingDetails->sum('paid'),
            'total_balance' => $billingDetails->sum('balance')
        ];
        
        return response()->json([
            'student' => $student,
            'billing_details' => $billingDetails,
            'totals' => $totals
        ]);
        
    } catch (\Exception $e) {
        Log::error('Error fetching student billing details: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        return response()->json([
            'error' => 'Failed to fetch billing details: ' . $e->getMessage()
        ], 500);
    }
}
  
public function postFeePayment(Request $request)
{
    try {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'admno' => 'required',
            'receiptdate' => 'required|date',
            'receiptno' => 'required|unique:tblreciept,receiptno',
            'pamount' => 'required|numeric|min:1',
            'pmethod' => 'required',
            'tcode' => 'nullable',
            'chequeno' => 'nullable',
            'bankn' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Get active period
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->value('ID');
        if (!$activePeriod) {
            return response()->json([
                'errors' => ['general' => ['No active period found']]
            ], 422);
        }

        // Get student outstanding fee items ordered by ID (earliest first)
        $feeItems = DB::table('managefee')
            ->where('admno', $request->admno)
            ->where('period', $activePeriod)
            ->where('balance', '>', 0)
            ->orderBy('ID', 'asc')
            ->get();

        if ($feeItems->isEmpty()) {
            return response()->json([
                'errors' => ['general' => ['No outstanding fee items found for this student']]
            ], 422);
        }

        // Start transaction
        DB::beginTransaction();

        try {
            // Save receipt
            

            // Process payment allocation
            $remainingAmount = $request->pamount;
            $updatedItems = [];

            foreach ($feeItems as $item) {
                if ($remainingAmount <= 0) {
                    break;
                }

                // Important: Use property names exactly as they appear in the database (case sensitive)
                // Use isset to check if properties exist before accessing them
                $currentBalance = isset($item->balance) ? $item->balance : 0;
                $currentPaid = isset($item->paid) ? $item->paid : 0;
                
                // Calculate how much can be allocated to this item
                $allocateAmount = min($remainingAmount, $currentBalance);
                
                // Update the item - FIX: Use ID instead of id to match the database case
                $itemId = isset($item->ID) ? $item->ID : (isset($item->id) ? $item->id : null);
                
                if ($itemId) {
                    DB::table('managefee')
                        ->where('ID', $itemId)
                        ->update([
                            'paid' => $currentPaid + $allocateAmount,
                            'balance' => $item->amount - ($currentPaid + $allocateAmount),
                            'status' => (($item->amount - ($currentPaid + $allocateAmount)) <= 0) ? 'Paid' : 'Partial',
                            'updated_at' => Carbon::now()
                        ]);
                    
                    // Track updated items for the response
                    $updatedItems[] = [
                        'ID' => $itemId,
                        'feeid' => isset($item->feeid) ? $item->feeid : null,
                        'amount' => isset($item->amount) ? $item->amount : 0,
                        'previous_paid' => $currentPaid,
                        'allocated' => $allocateAmount,
                        'new_paid' => $currentPaid + $allocateAmount,
                        'new_balance' => isset($item->amount) ? $item->amount - ($currentPaid + $allocateAmount) : 0,
                        'status' => (isset($item->amount) && ($item->amount - ($currentPaid + $allocateAmount)) <= 0) ? 'Paid' : 'Partial'
                    ];
                    
                    // Reduce the remaining amount
                    $remainingAmount -= $allocateAmount;
                }
            }

            $totalBalance = DB::table('managefee')
    ->where('admno', $request->admno)
    ->where('period', $activePeriod)
    ->sum('balance');
    
    $receipt = Receipts::create([
                'admno' => $request->admno,
                'period' => $activePeriod,
                'amount' => $request->pamount,
                'balanceasof' => $totalBalance,
                'pmode' => $request->pmethod,
                'tcode' => $request->tcode,
                'chequeno' => $request->chequeno,
                'bankn' => $request->bankn,
                'receiptno' => $request->receiptno,
                'receiptdate' => $request->receiptdate
            ]);


            DB::commit();

            // Re-fetch student billing details with the updated information
            $updatedBillingRequest = new Request(['admno' => $request->admno]);
            $updatedBilling = $this->getStudentBillingDetails($updatedBillingRequest);
            
            // Make sure the receipt data has basic info
            $receiptData = [
                'receiptno' => $request->receiptno,
                'receiptdate' => $request->receiptdate,
                'amount' => $request->pamount,
                'pmode' => $request->pmethod
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Payment posted successfully',
                'receipt_data' => $receiptData,
                'updated_items' => $updatedItems,
                'billing_details' => $updatedBilling->original,
                'remaining_unallocated' => $remainingAmount > 0 ? $remainingAmount : 0
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    } catch (\Exception $e) {
        Log::error('Error posting fee payment: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        return response()->json([
            'error' => 'Failed to post payment: ' . $e->getMessage()
        ], 500);
    }
}
public function getStudentReceipts(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'admno' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Get active period
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->value('ID');
        
        if (!$activePeriod) {
            return response()->json([
                'error' => 'No active period found'
            ], 422);
        }

        // Fetch receipts with payment method names
        $receipts = DB::table('tblreciept as r')
            ->leftJoin('pmethods as pm', 'r.pmode', '=', 'pm.ID')
            ->select([
                'r.ID',
                'r.admno',
                'r.receiptno',
                'r.receiptdate',
                'r.amount',
                'r.pmode',
                'r.balanceasof',
                'pm.pname as payment_method',
                'r.tcode',
                'r.chequeno',
                'r.bankn',
                'r.created_at'
            ])
            ->where('r.admno', $request->admno)
            ->where('r.period', $activePeriod)
            ->orderBy('r.receiptdate', 'desc')
            ->orderBy('r.created_at', 'desc')
            ->get();

        // Calculate total amount
        $totalAmount = $receipts->sum('amount');

        return response()->json([
            'success' => true,
            'receipts' => $receipts,
            'total_amount' => $totalAmount,
            'count' => $receipts->count()
        ]);

    } catch (\Exception $e) {
        Log::error('Error fetching student receipts: ' . $e->getMessage());
        return response()->json([
            'error' => 'Failed to fetch receipts: ' . $e->getMessage()
        ], 500);
    }
}

public function printStudentStatement(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'admno' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Get school information
        $school = Structure::first();
        
        // Get active period
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->first();
        
        if (!$activePeriod) {
            return response()->json([
                'error' => 'No active period found'
            ], 422);
        }

        // Get student information
        $student = DB::table('students as s')
            ->leftJoin('tblclasses as c', 's.claid', '=', 'c.ID')
            ->leftJoin('streams as st', 'c.stid', '=', 'st.ID')
            ->select([
                's.admno',
                's.sirname',
                's.othername',
                'c.claname',
                'st.strmname'
            ])
            ->where('s.admno', $request->admno)
            ->first();

        if (!$student) {
            return response()->json([
                'error' => 'Student not found'
            ], 404);
        }

        // Fetch receipts (same query as your existing method)
        $receipts = DB::table('tblreciept as r')
            ->leftJoin('pmethods as pm', 'r.pmode', '=', 'pm.ID')
            ->select([
                'r.ID',
                'r.admno',
                'r.receiptno',
                'r.receiptdate',
                'r.amount',
                'r.pmode',
                'pm.pname as payment_method',
                'r.tcode',
                'r.chequeno',
                'r.bankn',
                'r.created_at'
            ])
            ->where('r.admno', $request->admno)
            ->where('r.period', $activePeriod->ID)
            ->orderBy('r.receiptdate', 'desc')
            ->orderBy('r.created_at', 'desc')
            ->get();

        // Calculate total amount
        $totalAmount = $receipts->sum('amount');

        // Prepare data for PDF
        $data = [
            'school' => $school,
            'student' => $student,
            'receipts' => $receipts,
            'totalAmount' => $totalAmount,
            'period' => $activePeriod,
            'generatedDate' => now(),
            'receiptCount' => $receipts->count()
        ];

        // Generate PDF
        $pdf = Pdf::loadView('reports.student_statement', $data);
        $pdf->setPaper('A4', 'portrait');
        
        // Create filename
        $filename = 'statement_' . $student->admno . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);

    } catch (\Exception $e) {
        Log::error('Error generating student statement: ' . $e->getMessage());
        return response()->json([
            'error' => 'Failed to generate statement: ' . $e->getMessage()
        ], 500);
    }
}

public function previewStudentStatement(Request $request)
{
    try {
        // Same logic as above but return inline instead of download
        $validator = Validator::make($request->all(), [
            'admno' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Get school information
        $school = Structure::first();
        
        // Get active period
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->first();
        
        if (!$activePeriod) {
            return response()->json([
                'error' => 'No active period found'
            ], 422);
        }

        // Get student information
        $student = DB::table('students as s')
            ->leftJoin('tblclasses as c', 's.claid', '=', 'c.ID')
            ->leftJoin('streams as st', 'c.stid', '=', 'st.ID')
            ->select([
                's.admno',
                's.sirname',
                's.othername',
                'c.claname',
                'st.strmname',
                's.admdate'
            ])
            ->where('s.admno', $request->admno)
            ->first();

        if (!$student) {
            return response()->json([
                'error' => 'Student not found'
            ], 404);
        }

        // Fetch receipts
        $receipts = DB::table('tblreciept as r')
            ->leftJoin('pmethods as pm', 'r.pmode', '=', 'pm.ID')
            ->select([
                'r.ID',
                'r.admno',
                'r.receiptno',
                'r.receiptdate',
                'r.amount',
                'r.pmode',
                'r.balanceasof',
                'pm.pname as payment_method',
                'r.tcode',
                'r.chequeno',
                'r.bankn',
                'r.created_at'
            ])
            ->where('r.admno', $request->admno)
            ->where('r.period', $activePeriod->ID)
            ->orderBy('r.ID', 'asc')
            
            ->get();

        $totalAmount = $receipts->sum('amount');


        $invoinces = DB::table('managefee as r')
            ->leftJoin('feeitems as pm', 'r.feeid', '=', 'pm.ID')
            ->select([
                'r.ID',
                'r.admno',
                'r.created_at',
                'r.amount',
                'r.paid',
                'r.balance',
                'r.feeid',
                'pm.feename as feedesc'
            ])
            ->where('r.admno', $request->admno)
            ->where('r.period', $activePeriod->ID)
            ->orderBy('r.ID', 'asc')
            ->get();
            $totalfee = $invoinces->sum('amount');
             $totalpaid = $invoinces->sum('paid');
              $totalbal = $invoinces->sum('balance');

        $data = [
            'school' => $school,
            'student' => $student,
            'receipts' => $receipts,
            'invoinces' => $invoinces,
            'totalAmount' => $totalAmount,
            'totalfee' => $totalfee,
            'totalpaid' => $totalpaid,
            'totalbal' => $totalbal,
            'period' => $activePeriod,
            'generatedDate' => now(),
            'receiptCount' => $receipts->count(),
            'invoiceCount' => $invoinces->count()
        ];

        $pdf = Pdf::loadView('reports.student_statement', $data);
$pdf->setPaper('A4', 'portrait');

return response($pdf->output(), 200)
    ->header('Content-Type', 'application/pdf')
    ->header('Content-Disposition', 'inline; filename="statement_preview.pdf"');


    } catch (\Exception $e) {
        Log::error('Error previewing student statement: ' . $e->getMessage());
        return response()->json([
            'error' => 'Failed to preview statement: ' . $e->getMessage()
        ], 500);
    }
}

public function previewStudentreceipt(Request $request)
{
    try {
        // Same logic as above but return inline instead of download
        $validator = Validator::make($request->all(), [
            'admno' => 'required',
            'receiptId' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Get school information
        $school = Structure::first();
        
        // Get active period
        $activePeriod = DB::table('tblperiods')->where('pstatus', 'Active')->first();
        
        if (!$activePeriod) {
            return response()->json([
                'error' => 'No active period found'
            ], 422);
        }

        // Get student information
        $student = DB::table('students as s')
            ->leftJoin('tblclasses as c', 's.claid', '=', 'c.ID')
            ->leftJoin('streams as st', 'c.stid', '=', 'st.ID')
            ->select([
                's.admno',
                's.sirname',
                's.othername',
                'c.claname',
                'st.strmname'
            ])
            ->where('s.admno', $request->admno)
            ->first();

        if (!$student) {
            return response()->json([
                'error' => 'Student not found'
            ], 404);
        }

        // Fetch receipts
        $receipts = DB::table('tblreciept as r')
            ->leftJoin('pmethods as pm', 'r.pmode', '=', 'pm.ID')
            ->select([
                'r.ID',
                'r.admno',
                'r.receiptno',
                'r.receiptdate',
                'r.amount',
                'r.pmode',
                'r.balanceasof',
                'pm.pname as payment_method',
                'r.tcode',
                'r.chequeno',
                'r.bankn',
                'r.created_at'
            ])
            ->where('r.admno', $request->admno)
            ->where('r.ID', $request->receiptId)
            ->where('r.period', $activePeriod->ID)
            ->orderBy('r.receiptdate', 'desc')
            ->orderBy('r.created_at', 'desc')
            ->get();

        $totalAmount = $receipts->sum('amount');

        $data = [
            'school' => $school,
            'student' => $student,
            'receipts' => $receipts,
            'totalAmount' => $totalAmount,
            'period' => $activePeriod,
            'generatedDate' => now(),
            'receiptCount' => $receipts->count()
        ];

        $pdf = Pdf::loadView('reports.student_receipt', $data);
$pdf->setPaper('A4', 'portrait');

return response($pdf->output(), 200)
    ->header('Content-Type', 'application/pdf')
    ->header('Content-Disposition', 'inline; filename="statement_receipt.pdf"');


    } catch (\Exception $e) {
        Log::error('Error previewing student reciept: ' . $e->getMessage());
        return response()->json([
            'error' => 'Failed to preview statement: ' . $e->getMessage()
        ], 500);
    }
}

}