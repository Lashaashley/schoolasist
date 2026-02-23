<x-custom-admin-layout>
    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>

<style>
/* ALERT */
.custom-alert {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 280px;
    z-index: 9999;
    transform: translateX(400px);
    transition: all 0.4s ease;
}
.custom-alert.show { transform: translateX(0); }

/* STAT CARDS */
.stat-card {
    border-radius: 15px;
    padding: 25px;
    color: #fff;
    text-align: left;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.stat-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }

.stat-card h3 { margin:0; font-weight:700; font-size:1.5rem; }
.stat-card small { opacity:0.85; display:block; margin-bottom:8px; font-size:0.95rem; }

.stat-icon {
    width: 55px;
    height: 55px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    font-size: 1.5rem;
}

/* TABLES */
.table-modern th { background:#f8f9fa; font-weight:600; font-size:13px; text-transform:uppercase; }
.badge-method, .badge-status { padding:5px 10px; border-radius:20px; font-size:12px; display:inline-block; }

/* BUTTONS */
.btn-gradient {
    color: #fff;
    border: none;
    transition: all 0.3s ease;
}
.btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }

/* RESPONSIVE */
@media (max-width: 768px) {
    .stat-card { padding: 15px; font-size: 0.9rem; flex-direction: column; align-items: flex-start; }
    .stat-card h3 { font-size: 1.2rem; margin-top:5px; }
    .d-flex.gap-2 { flex-wrap: wrap; gap:5px; }
    .table-responsive { overflow-x: auto; }
    #searchInput, #filterDate { margin-bottom:10px; }
}
</style>

<div class="pd-ltr-20">

    <!-- ALERT -->
    <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display:none;">
        <strong id="alert-title"></strong>
        <span id="alert-message"></span>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-20">
        <div>
            <h4 class="mb-0">Supplier Payments</h4>
            <small class="text-muted">Track all payments, partial or full, and generate receipts/reports</small>
        </div>
        <div class="d-flex gap-2 flex-wrap mt-2 mt-md-0">
            <button class="btn btn-gradient" style="background: linear-gradient(135deg,#28a745,#218838);" onclick="window.print()">
                <i data-feather="file-text" class="me-1"></i> Generate Report
            </button>
            <button class="btn btn-gradient" style="background: linear-gradient(135deg,#007bff,#0069d9);" data-toggle="modal" data-target="#paymentModal">
                <i data-feather="plus" class="me-1"></i> Record Payment
            </button>
        </div>
    </div>

    <!-- STAT CARDS -->
    @php
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
    @endphp
    <div class="row mb-30">
        <div class="col-sm-6 col-md-3 mb-15">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <div>
                    <small>Total Payments</small>
                    <h3>{{ number_format($payments->sum('amount_paid'),2) }}</h3>
                </div>
                <div class="stat-icon">
                    <i data-feather="dollar-sign"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 mb-15">
            <div class="stat-card" style="background: linear-gradient(135deg, #42e695, #3bb2b8);">
                <div>
                    <small>This Month</small>
                    <h3>{{ number_format($payments->whereBetween('payment_date',[$monthStart,$monthEnd])->sum('amount_paid'),2) }}</h3>
                </div>
                <div class="stat-icon">
                    <i data-feather="calendar"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 mb-15">
            <div class="stat-card" style="background: linear-gradient(135deg, #f7971e, #ffd200);">
                <div>
                    <small>Total Transactions</small>
                    <h3>{{ $payments->count() }}</h3>
                </div>
                <div class="stat-icon">
                    <i data-feather="repeat"></i>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 mb-15">
            <div class="stat-card" style="background: linear-gradient(135deg, #f85032, #e73827);">
                <div>
                    <small>Pending Invoices</small>
                    <h3>{{ $approvedInvoices->count() }}</h3>
                </div>
                <div class="stat-icon">
                    <i data-feather="file-text"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card-box pd-20 mb-20">
        <div class="row">
            <div class="col-sm-6 col-md-4 mb-10">
                <div class="input-group">
                    <!--<span class="input-group-text"><i data-feather="search"></i></span>-->
                    <input type="text" id="searchInput" class="form-control" placeholder="Search supplier or invoice...">
                </div>
            </div>
            <div class="col-sm-6 col-md-3 mb-10">
                <div class="input-group">
                   <!--< span class="input-group-text"><i data-feather="calendar"></i></span>-->
                    <input type="date" id="filterDate" class="form-control">
                </div>
            </div>
        </div>
    </div>

    <div class="card-box pd-20">
        <div class="table-responsive">
            <table class="table table-hover table-modern table-striped">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Supplier</th>
                        <th>Total Invoice</th>
                        <th>Amount Paid</th>
                        <th>Balance</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    @php
                        $invoice = $payment->invoice;
                        $balance = $invoice->total_amount - $invoice->payments->sum('amount_paid');
                        $status = $balance == 0 ? 'Paid' : 'Partial';
                    @endphp
                    <tr>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>{{ $payment->supplier->name }}</td>
                        <td>{{ number_format($invoice->total_amount,2) }}</td>
                        <td>{{ number_format($payment->amount_paid,2) }}</td>
                        <td>{{ number_format($balance,2) }}</td>
                        <td><span class="badge badge-info badge-method">{{ ucfirst(str_replace('_',' ',$payment->payment_method)) }}</span></td>
                        <td><span class="badge {{ $status == 'Paid' ? 'badge-success' : 'badge-warning' }} badge-status">{{ $status }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                        <td>{{ $payment->payment_reference ?? '-' }}</td>
                        <td>
                            <a href="{{ route('suppliers.payment.receipt',$payment->id) }}" class="btn btn-gradient" style="background: linear-gradient(135deg,#17a2b8,#138496);" target="_blank">
                                <i data-feather="file-text" class="me-1"></i> Receipt
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- RECORD PAYMENT MODAL -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form id="record-payment-form">
                @csrf
                <input type="hidden" name="invoice_id" id="payment_invoice_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Record Payment</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-10">
                            <label>Invoice</label>
                            <select name="invoice_id" id="invoice_select" class="form-control" required>
                                <option value="">Select Invoice</option>
                                @foreach($approvedInvoices as $invoice)
                                    @php
                                        $balance = $invoice->total_amount - $invoice->payments->sum('amount_paid');
                                    @endphp
                                    <option value="{{ $invoice->id }}">
                                        {{ $invoice->invoice_number }} (Balance: {{ number_format($balance,2) }})
                                    </option>
                                @endforeach
                            </select>
                            <small id="invoice_id-error" class="text-danger"></small>
                        </div>
                        <div class="col-md-6 mb-10">
                            <label>Amount</label>
                            <input type="number" step="0.01" name="amount_paid" class="form-control" required>
                            <small id="amount_paid-error" class="text-danger"></small>
                        </div>
                        <div class="col-md-6 mb-10">
                            <label>Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="mobile_money">Mobile Money</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-10">
                            <label>Reference</label>
                            <input type="text" name="payment_reference" class="form-control">
                        </div>
                        <div class="col-md-6 mb-10">
                            <label>Date</label>
                            <input type="date" name="payment_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-gradient w-100" style="background: linear-gradient(135deg,#007bff,#0069d9);">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    feather.replace(); // Initialize Feather Icons
</script>

</x-custom-admin-layout>