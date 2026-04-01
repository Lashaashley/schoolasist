<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceInvitationMail;
use App\Models\Supplier;
use App\Models\Lpo;
use App\Models\SupplierInvoice;
use App\Models\SupplierPayment;
use App\Models\SupplierInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SupplierPaymentsExport;

class SupplierController extends Controller
{

/*
|--------------------------------------------------------------------------
| SUPPLIERS
|--------------------------------------------------------------------------
*/

public function create()
{
    return view('suppliers.add_supplier');
}

public function manage()
{
    return view('suppliers.manage_suppliers');
}

public function getSuppliers()
{
    return response()->json([
        'data' => Supplier::latest()->get([
            'id','name','company','email','phone','address','profile',
            'bank_name','account_name','account_number',
            'mpesa_paybill','mpesa_till','mpesa_phone'
        ])
    ]);
}

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name'=>'required|string|max:255',
        'email'=>'nullable|email|unique:suppliers,email',
        'phone'=>'nullable|string|max:20',
        'address'=>'nullable|string|max:500',
        'company'=>'nullable|string|max:255',
        'profile'=>'nullable|image|max:2048',
        // Payment fields
        'bank_name'=>'nullable|string|max:255',
        'account_name'=>'nullable|string|max:255',
        'account_number'=>'nullable|string|max:50',
        'mpesa_paybill'=>'nullable|string|max:20',
        'mpesa_till'=>'nullable|string|max:20',
        'mpesa_phone'=>'nullable|string|max:20',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors'=>$validator->errors()], 422);
    }

    $data = $validator->validated();

    // Handle profile upload
    if ($request->hasFile('profile')) {
        $data['profile'] = $request->file('profile')->store('supplier-profiles','public');
    }

    Supplier::create($data);

    return response()->json(['message'=>'Supplier added successfully'], 201);
}

/**
 * Update an existing supplier
 */
public function update(Request $request, $id)
{
    $supplier = Supplier::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'name'=>'required|string|max:255',
        'email'=>'nullable|email|unique:suppliers,email,'.$id,
        'phone'=>'nullable|string|max:20',
        'address'=>'nullable|string|max:500',
        'company'=>'nullable|string|max:255',
        // Payment fields
        'bank_name'=>'nullable|string|max:255',
        'account_name'=>'nullable|string|max:255',
        'account_number'=>'nullable|string|max:50',
        'mpesa_paybill'=>'nullable|string|max:20',
        'mpesa_till'=>'nullable|string|max:20',
        'mpesa_phone'=>'nullable|string|max:20',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors'=>$validator->errors()], 422);
    }

    $supplier->update($validator->validated());

    return response()->json(['message'=>'Supplier updated successfully']);
}

public function destroy($id)
{
    Supplier::findOrFail($id)->delete();
    return response()->json(['message'=>'Supplier deleted successfully']);
}


/*
|--------------------------------------------------------------------------
| PAYMENTS
|--------------------------------------------------------------------------
*/

public function payments(Request $request)
{
    $query = SupplierPayment::with(['invoice','supplier']);

    if($request->supplier_id){
        $query->where('supplier_id',$request->supplier_id);
    }

    if($request->from_date && $request->to_date){
        $query->whereBetween('payment_date',[$request->from_date,$request->to_date]);
    }

    $payments = $query->latest()->get();

    $totalPaid = SupplierPayment::sum('amount_paid');
    $totalOutstanding = SupplierInvoice::sum('balance');
    $thisMonth = SupplierPayment::whereMonth('payment_date',now()->month)->sum('amount_paid');

    $suppliers = Supplier::all();

    $approvedInvoices = SupplierInvoice::whereIn('status',['approved','paid'])
        ->where('balance','>',0)->get();

    return view('suppliers.payments',compact(
        'payments','totalPaid','totalOutstanding','thisMonth','suppliers','approvedInvoices'
    ));
}

public function storePayment(Request $request)
{
    $validator = Validator::make($request->all(),[
        'invoice_id'=>'required|exists:supplier_invoices,id',
        'amount_paid'=>'required|numeric|min:0.01',
        'payment_method'=>'required|string',
        'payment_date'=>'required|date'
    ]);

    if($validator->fails()){
        return response()->json(['errors'=>$validator->errors()],422);
    }

    return DB::transaction(function() use ($request){

        $invoice = SupplierInvoice::lockForUpdate()->findOrFail($request->invoice_id);

        if($request->amount_paid > $invoice->balance){
            return response()->json([
                'errors'=>['amount_paid'=>['Payment exceeds remaining balance']]
            ],422);
        }

        $payment = SupplierPayment::create([
            'invoice_id'=>$invoice->id,
            'supplier_id'=>$invoice->supplier_id,
            'amount_paid'=>$request->amount_paid,
            'payment_method'=>$request->payment_method,
            'payment_reference'=>$request->payment_reference,
            'payment_date'=>$request->payment_date,
            'recorded_by'=>auth()->id()
        ]);

        $invoice->amount_paid += $request->amount_paid;
        $invoice->balance -= $request->amount_paid;

        if($invoice->balance <= 0){
            $invoice->balance = 0;
            $invoice->status = 'paid';
        }

        $invoice->save();

        return response()->json([
            'message'=>'Payment recorded successfully',
            'payment'=>$payment
        ]);
    });
}

public function generateReceipt($paymentId)
{
    $payment = SupplierPayment::with(['invoice','supplier'])->findOrFail($paymentId);

    $pdf = Pdf::loadView('suppliers.receipt',compact('payment'));

    return $pdf->stream("Receipt_{$payment->invoice->invoice_number}.pdf");
}

public function exportPayments()
{
    return Excel::download(new SupplierPaymentsExport,'Supplier_Payments.xlsx');
}


/*
|--------------------------------------------------------------------------
| INVOICES
|--------------------------------------------------------------------------
*/

public function indexInvoices(Request $request)
{
    $query = SupplierInvoice::with('supplier');

    if($request->supplier_id){
        $query->where('supplier_id',$request->supplier_id);
    }

    if($request->status){
        $query->where('status',$request->status);
    }

    $invoices = $query->latest()->get();
    $suppliers = Supplier::all();

    return view('suppliers.invoices',compact('invoices','suppliers'));
}

public function createInvoice()
{
    $suppliers = Supplier::all();
    return view('suppliers.add_invoice',compact('suppliers'));
}

public function editInvoice($id)
{
    $invoice = SupplierInvoice::findOrFail($id);
    $suppliers = Supplier::all();
    return view('suppliers.edit_invoice',compact('invoice','suppliers'));
}

public function storeInvoice(Request $request)
{
    $validator = Validator::make($request->all(),[
        'supplier_id'=>'required|exists:suppliers,id',
        'invoice_number'=>'required|string|unique:supplier_invoices,invoice_number',
        'total_amount'=>'required|numeric|min:0.01',
        'due_date'=>'required|date',
        'status'=>'required|in:pending,approved,paid'
    ]);

    if($validator->fails()){
        return response()->json(['errors'=>$validator->errors()],422);
    }

    $data = $validator->validated();
    $data['amount_paid'] = 0;
    $data['balance'] = $data['total_amount'];

    SupplierInvoice::create($data);

    return response()->json(['message'=>'Invoice created successfully'],201);
}

public function updateInvoice(Request $request,$id)
{
    $invoice = SupplierInvoice::findOrFail($id);

    $validator = Validator::make($request->all(),[
        'supplier_id'=>'required|exists:suppliers,id',
        'invoice_number'=>'required|string|unique:supplier_invoices,invoice_number,'.$id,
        'total_amount'=>'required|numeric|min:0.01',
        'due_date'=>'required|date',
        'status'=>'required|in:pending,approved,paid'
    ]);

    if($validator->fails()){
        return response()->json(['errors'=>$validator->errors()],422);
    }

    $invoice->update($validator->validated());

    $invoice->balance = max(0,$invoice->total_amount - $invoice->amount_paid);

    if($invoice->balance == 0){
        $invoice->status = 'paid';
    }

    $invoice->save();

    return response()->json(['message'=>'Invoice updated successfully']);
}

public function destroyInvoice($id)
{
    SupplierInvoice::findOrFail($id)->delete();
    return response()->json(['message'=>'Invoice deleted successfully']);
}


public function submitInvoiceForm(Request $request, SupplierInvitation $invitation)
{
    $validator = Validator::make($request->all(), [
        'invoice_number' => 'required|string|unique:supplier_invoices,invoice_number',
        'invoice_date' => 'required|date',
        'attachment' => 'nullable|file|max:2048',
        'description' => 'nullable|string|max:500',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
                         ->withErrors($validator)
                         ->withInput();
    }

    // Create invoice using the invitation details
    $invoice = new SupplierInvoice();
    $invoice->supplier_id = $invitation->supplier_id;
    $invoice->lpo_id = $invitation->lpo_id;
    $invoice->invoice_number = $request->invoice_number;
    $invoice->invoice_date = $request->invoice_date;
    $invoice->description = $request->description;
    $invoice->total_amount = $invitation->amount; // Use amount from invitation
    $invoice->balance = $invitation->amount;
    $invoice->amount_paid = 0;
    $invoice->status = 'pending';

    $invoice->due_date = $invitation->expires_at ?? now()->addDays(30);


    if ($request->hasFile('attachment')) {
        $invoice->attachment = $request->file('attachment')->store('invoices','public');
    }

    $invoice->save();

    // Mark the invitation as responded
    $invitation->responded = 1;
    $invitation->save();

    return redirect()->back()->with('success', 'Invoice submitted successfully!');
}


/*
|--------------------------------------------------------------------------
| INVITATIONS
|--------------------------------------------------------------------------
*/

public function supplierInvitations()
{
    $suppliers = Supplier::all();
    $lpos = Lpo::all();

    $invitations = SupplierInvitation::with('supplier')->latest()->get();

    return view('suppliers.create_invitations',compact('suppliers','lpos','invitations'));
}




public function storeInvoiceInvitation(Request $request)
{
    $validator = Validator::make($request->all(),[
        'supplier_id'=>'required|exists:suppliers,id',
        'lpo_id'=>'required|exists:lpos,id',
        'category'=>'required|string|max:255',
        'message'=>'nullable|string',
        'expires_at'=>'nullable|date'
    ]);

    if($validator->fails()){
        return response()->json(['errors'=>$validator->errors()],422);
    }

    // Fetch LPO with items and supplier
    $lpo = Lpo::with('items','supplier')->findOrFail($request->lpo_id);

    // Prepare items list
    $items = $lpo->items->map(fn($item) => $item->product_name)->toArray();

    // Calculate totals from LPO items
    $quantity = $lpo->items->sum('quantity');  
    $unit_price = $lpo->items->avg('unit_price');  // <-- use correct column
    $amount = $lpo->items->sum(fn($item) => $item->quantity * $item->unit_price);

    // Create invitation
    $invitation = SupplierInvitation::create([
        'supplier_id'=>$request->supplier_id,
        'lpo_id'=>$request->lpo_id,
        'category'=>$request->category,
        'items'=>implode(', ', $items),
        'quantity'=>$quantity,
        'unit_price'=>$unit_price,
        'amount'=>$amount,
        'message'=>$request->message,
        'expires_at'=>$request->expires_at,
        'responded'=>0
    ]);

    $invoiceFormLink = route('supplier.invoice.form', $invitation->id);

    // Send email
    if($invitation->supplier?->email){
        try{
            Mail::to($invitation->supplier->email)
                ->send(new InvoiceInvitationMail($invitation, $invoiceFormLink));
        }catch(\Exception $e){
            Log::error('Failed sending invitation email: '.$e->getMessage());
        }
    }

    // Return JSON for JS
    return response()->json([
        'message'=>'Invitation created successfully',
        'invitation'=>[
            'id'=>$invitation->id,
            'supplier_name'=>$invitation->supplier->name,
            'company'=>$invitation->supplier->company,
            'category'=>$invitation->category,
            'items'=>$invitation->items,
            'quantity'=>$invitation->quantity,
            'unit_price'=>$invitation->unit_price,
            'amount'=>$invitation->amount,
            'message'=>$invitation->message ?? '-',
            'expires_at'=>optional($invitation->expires_at)->format('d M Y'),
            'sent_at'=>$invitation->created_at->format('d M Y H:i')
        ]
    ]);
}




public function getLpoDetails($id)
{
    $lpo = Lpo::with(['supplier','items'])->findOrFail($id);

    $items = [];

    foreach($lpo->items as $item){
        $items[] = [
            'product'=>$item->product_name,
            'quantity'=>$item->quantity,
            'price'=>$item->unit_price,
            'total'=>$item->total
        ];
    }

    return response()->json([
        'supplier_id'=>$lpo->supplier->id,
        'supplier_name'=>$lpo->supplier->name,
        'company'=>$lpo->supplier->company,
        'category'=>$lpo->category,
        'unit_price'=>$lpo->items->avg('unit_price'),
        'quantity'=>$lpo->items->sum('quantity'),
        'amount'=>$lpo->items->sum('total'),
        'items'=>$items
    ]);
}

public function showInvoiceForm($id)
{
    // Load the invitation with its supplier and LPO (if any)
    $invitation = SupplierInvitation::with(['supplier', 'lpo'])->find($id);

    // If the invitation does not exist, redirect to a safe page
    if (!$invitation) {
        return redirect()->route('supplier.list')
                         ->with('error', 'Invitation not found.');
    }

    // Optional: If LPO is missing, just show a message instead of redirecting
    if (!$invitation->lpo) {
        return view('suppliers.invoice_form', [
            'invitation' => $invitation,
            'lpoMissing' => true, // flag to show a message in the Blade
        ]);
    }

    // If everything exists, load normally
    return view('suppliers.invoice_form', [
        'invitation' => $invitation,
        'lpoMissing' => false,
    ]);
}




}