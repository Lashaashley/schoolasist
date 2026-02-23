<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceInvitationMail;
use App\Models\Supplier;
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
            'data' => Supplier::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'company' => 'nullable|string|max:255',
            'profile' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if ($request->hasFile('profile')) {
            $data['profile'] = $request->file('profile')
                ->store('supplier-profiles', 'public');
        }

        Supplier::create($data);

        return response()->json(['message' => 'Supplier added successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:suppliers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'company' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $supplier->update($validator->validated());

        return response()->json(['message' => 'Supplier updated successfully']);
    }

    public function destroy($id)
    {
        Supplier::findOrFail($id)->delete();
        return response()->json(['message' => 'Supplier deleted successfully']);
    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENTS
    |--------------------------------------------------------------------------
    */

    public function payments(Request $request)
    {
        $query = SupplierPayment::with(['invoice', 'supplier']);

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->from_date && $request->to_date) {
            $query->whereBetween('payment_date', [$request->from_date, $request->to_date]);
        }

        $payments = $query->latest()->get();

        $totalPaid = SupplierPayment::sum('amount_paid');
        $totalOutstanding = SupplierInvoice::sum('balance');
        $thisMonth = SupplierPayment::whereMonth('payment_date', now()->month)
            ->sum('amount_paid');

        $suppliers = Supplier::all();

        $approvedInvoices = SupplierInvoice::whereIn('status', ['approved', 'paid'])
            ->where('balance', '>', 0)
            ->get();

        return view('suppliers.payments', compact(
            'payments',
            'totalPaid',
            'totalOutstanding',
            'thisMonth',
            'suppliers',
            'approvedInvoices'
        ));
    }

    public function storePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:supplier_invoices,id',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request) {

            $invoice = SupplierInvoice::lockForUpdate()
                ->findOrFail($request->invoice_id);

            if ($request->amount_paid > $invoice->balance) {
                return response()->json([
                    'errors' => ['amount_paid' => ['Payment exceeds remaining balance']]
                ], 422);
            }

            $payment = SupplierPayment::create([
                'invoice_id' => $invoice->id,
                'supplier_id' => $invoice->supplier_id,
                'amount_paid' => $request->amount_paid,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'payment_date' => $request->payment_date,
                'recorded_by' => auth()->id(),
            ]);

            $invoice->amount_paid += $request->amount_paid;
            $invoice->balance -= $request->amount_paid;

            if ($invoice->balance <= 0) {
                $invoice->balance = 0;
                $invoice->status = 'paid';
            }

            $invoice->save();

            return response()->json([
                'message' => 'Payment recorded successfully',
                'payment' => $payment
            ]);
        });
    }

    public function generateReceipt($paymentId)
    {
        $payment = SupplierPayment::with(['invoice', 'supplier'])
            ->findOrFail($paymentId);

        $pdf = Pdf::loadView('suppliers.receipt', compact('payment'));
        return $pdf->stream("Receipt_{$payment->invoice->invoice_number}.pdf");
    }

    public function exportPayments()
    {
        return Excel::download(
            new SupplierPaymentsExport,
            'Supplier_Payments.xlsx'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | INVOICES
    |--------------------------------------------------------------------------
    */

    public function indexInvoices(Request $request)
    {
        $query = SupplierInvoice::with('supplier');

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest()->get();
        $suppliers = Supplier::all();

        return view('suppliers.invoices', compact('invoices', 'suppliers'));
    }

    public function createInvoice()
    {
        $suppliers = Supplier::all();
        return view('suppliers.add_invoice', compact('suppliers'));
    }

    public function editInvoice($id)
    {
        $invoice = SupplierInvoice::findOrFail($id);
        $suppliers = Supplier::all();
        return view('suppliers.edit_invoice', compact('invoice', 'suppliers'));
    }

    public function storeInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|unique:supplier_invoices,invoice_number',
            'total_amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,approved,paid',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['amount_paid'] = 0;
        $data['balance'] = $data['total_amount'];

        SupplierInvoice::create($data);

        return response()->json(['message' => 'Invoice created successfully'], 201);
    }

    public function updateInvoice(Request $request, $id)
    {
        $invoice = SupplierInvoice::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|unique:supplier_invoices,invoice_number,' . $id,
            'total_amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,approved,paid',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $invoice->update($validator->validated());

        $invoice->balance = max(0, $invoice->total_amount - $invoice->amount_paid);

        if ($invoice->balance == 0) {
            $invoice->status = 'paid';
        }

        $invoice->save();

        return response()->json(['message' => 'Invoice updated successfully']);
    }

    public function destroyInvoice($id)
    {
        SupplierInvoice::findOrFail($id)->delete();
        return response()->json(['message' => 'Invoice deleted successfully']);
    }


   

    public function supplierInvitations()
{
    $suppliers = Supplier::all();
    $invitations = SupplierInvitation::with('supplier', 'invoice')
        ->latest()
        ->get();

    return view('suppliers.create_invitations', compact('suppliers', 'invitations'));
}

public function storeInvoiceInvitation(Request $request)
{
    $validator = Validator::make($request->all(), [
        'supplier_id' => 'required|exists:suppliers,id',
        'category' => 'required|string|max:255',
        'expires_at' => 'nullable|date',
        'message' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $invitation = SupplierInvitation::create($validator->validated());
    $invitation->load('supplier');

    $invoiceFormLink = route('supplier.invoice.form', $invitation->id);


    $emailSent = false;
    $emailError = null;
if ($invitation->supplier?->email) {
        try {
            Mail::to($invitation->supplier->email)
                ->send(new InvoiceInvitationMail($invitation, $invoiceFormLink));

            // Check if any failures occurred
            $emailSent = count(Mail::failures()) === 0;
            if (!$emailSent) {
                $emailError = 'Email failed to send.';
                Log::error("Invitation email failed for supplier ID: {$invitation->supplier->id}");
            }

        } catch (\Exception $e) {
            $emailError = $e->getMessage();
            Log::error('Failed to send invitation: ' . $emailError);
        }
    } else {
        $emailError = 'Supplier email not found.';
    }

       return response()->json([
        'message' => $emailSent ? 'Invitation created and email sent successfully' 
                                : 'Invitation created, but email was not sent: ' . $emailError,
        'invitation' => $invitation,
        'email_sent' => $emailSent
    ]);


}

public function sendInvoiceInvitation(Request $request)
{
    $request->validate([
        'invitation_id' => 'required|exists:supplier_invitations,id'
    ]);

    $invitation = SupplierInvitation::with('supplier', 'invoice')
        ->findOrFail($request->invitation_id);

    if (!$invitation->supplier?->email) {
        return back()->with('error', 'Supplier email not found');
    }

    $invoiceFormLink = route('supplier.invoice.form', $invitation->id);

    try {
        Mail::to($invitation->supplier->email)
            ->send(new InvoiceInvitationMail($invitation, $invoiceFormLink));
        return back()->with('success', 'Invitation sent successfully');
    } catch (\Exception $e) {
        Log::error('Failed to send invitation: ' . $e->getMessage());
        return back()->with('error', 'Failed to send email');
    }
}

public function submitInvoiceForm(Request $request)
{
    $validator = Validator::make($request->all(), [
        'invitation_id' => 'required|exists:supplier_invitations,id',
        'invoice_number' => 'required|string',
        'invoice_date' => 'required|date',
        'due_date' => 'required|date',
        'total_amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string',
        'attachment' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $invitation = SupplierInvitation::findOrFail($request->invitation_id);

    if ($invitation->expires_at && now()->gt($invitation->expires_at)) {
        return response()->json([
            'errors' => ['expired' => ['Invitation has expired']]
        ], 422);
    }

    $data = $validator->validated();
    $data['supplier_id'] = $invitation->supplier_id;
    $data['category'] = $invitation->category;
    $data['amount_paid'] = 0;
    $data['balance'] = $data['total_amount'];
    $data['status'] = 'pending';

    if ($request->hasFile('attachment')) {
        $data['attachment'] = $request->file('attachment')
            ->store('supplier-invoices', 'public');
    }

    // Create the invoice
    $invoice = SupplierInvoice::create($data);

    // Link the invoice to the invitation
    $invitation->invoice_id = $invoice->id;
    $invitation->responded = true;
    $invitation->save();

    return response()->json([
        'message' => 'Invoice submitted successfully',
        'invoice_id' => $invoice->id
    ]);
}

public function showInvoiceForm(SupplierInvitation $invitation)
{
    // Optional: check expiration
    if ($invitation->expires_at && now()->gt($invitation->expires_at)) {
        abort(403, 'This invitation has expired.');
    }

    return view('suppliers.invoice_form', compact('invitation'));

    }
    public function approveInvoice($id)
{
    $invoice = SupplierInvoice::findOrFail($id);
    $invoice->status = 'approved';
    $invoice->save();
    return response()->json(['success' => true]);
}

public function rejectInvoice($id)
{
    $invoice = SupplierInvoice::findOrFail($id);
    $invoice->status = 'rejected';
    $invoice->save();
    return response()->json(['success' => true]);
}

public function markInvoicePaid($id)
{
    $invoice = SupplierInvoice::findOrFail($id);
    $invoice->status = 'paid';
    $invoice->balance = 0;
    $invoice->amount_paid = $invoice->total_amount;
    $invoice->save();
    return response()->json(['success' => true]);
}

}