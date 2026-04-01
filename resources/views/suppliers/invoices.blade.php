<x-custom-admin-layout>

<div class="pd-ltr-20">

    <h4 class="mb-4 text-primary fw-bold">Supplier Invoices</h4>

    {{-- ======================== --}}
    {{-- Summary Cards --}}
    {{-- ======================== --}}
    <div class="row mb-4">
        @php
            $pendingCount = $invoices->where('status','pending')->count();
            $approvedCount = $invoices->where('status','approved')->count();
            $paidCount = $invoices->where('status','paid')->count();
            $rejectedCount = $invoices->where('status','rejected')->count();
        @endphp
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Pending</h5>
                    <p class="card-text fw-bold" style="font-size:1.5rem;">{{ $pendingCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="card text-white bg-info shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Approved</h5>
                    <p class="card-text fw-bold" style="font-size:1.5rem;">{{ $approvedCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Paid</h5>
                    <p class="card-text fw-bold" style="font-size:1.5rem;">{{ $paidCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-2">
            <div class="card text-white bg-danger shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Rejected</h5>
                    <p class="card-text fw-bold" style="font-size:1.5rem;">{{ $rejectedCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================== --}}
    {{-- Report Table --}}
    {{-- ======================== --}}
    <div class="mb-4">
        <h5 class="fw-bold">Invoices Report</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Invoice Number</th>
                        <th>Supplier</th>
                        <th>Inv Date</th>
                        <th>Due Date</th>
                        <th>Total Amount (KES)</th>
                        <th>Amount to Pay (KES)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grandTotalAmount = 0;
                        $grandTotalToPay = 0;
                    @endphp
                    @forelse($invoices as $index => $invoice)
                        @php
                            $amountToPay = max($invoice->total_amount - $invoice->amount_paid, 0);
                            $grandTotalAmount += $invoice->total_amount;
                            $grandTotalToPay += $amountToPay;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>#{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->supplier->name }} ({{ $invoice->supplier->company }})</td>
                            <td>{{ optional($invoice->invoice_date)->format('d M Y') ?? 'N/A' }}</td>
                            <td>{{ optional($invoice->due_date)->format('d M Y') ?? 'N/A' }}</td>
                            <td>{{ number_format($invoice->total_amount,2) }}</td>
                            <td>{{ number_format($amountToPay,2) }}</td>
                            <td>
                                <span class="badge badge-{{ 
                                    $invoice->status=='pending'?'warning':
                                    ($invoice->status=='approved'?'info':
                                    ($invoice->status=='paid'?'success':'danger')) 
                                }}">{{ ucfirst($invoice->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No invoices found.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($invoices->count())
                <tfoot class="fw-bold table-light">
                    <tr>
                        <td colspan="5">Totals ({{ $invoices->count() }} invoices)</td>
                        <td>{{ number_format($grandTotalAmount,2) }}</td>
                        <td>{{ number_format($grandTotalToPay,2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ======================== --}}
    {{-- Tabs with full collapsible invoices --}}
    {{-- ======================== --}}
    <ul class="nav nav-tabs mb-3 border-bottom">
        @foreach(['pending','approved','paid','rejected'] as $tab)
            <li class="nav-item">
                <a class="nav-link {{ $tab=='pending'?'active':'' }}" 
                   data-toggle="tab" 
                   href="#{{ $tab }}">
                   {{ ucfirst($tab) }}
                </a>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">
        @foreach(['pending','approved','paid','rejected'] as $status)
        <div class="tab-pane fade {{ $status=='pending'?'show active':'' }}" id="{{ $status }}">
            
            <div class="row">
                @forelse($invoices->where('status',$status) as $invoice)
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0 rounded">

                        {{-- Header with + / - --}}
                        <a class="d-block text-decoration-none" 
                           data-toggle="collapse" 
                           href="#invoice-{{ $invoice->id }}" 
                           role="button" 
                           aria-expanded="false" 
                           aria-controls="invoice-{{ $invoice->id }}">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>#{{ $invoice->invoice_number }}</strong> | 
                                    {{ $invoice->supplier->name }} - {{ $invoice->supplier->company }}
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-{{ 
                                        $status=='pending'?'warning':
                                        ($status=='approved'?'info':
                                        ($status=='paid'?'success':'danger')) 
                                    }}">{{ ucfirst($status) }}</span>
                                    <span class="ml-2 toggle-icon">+</span>
                                </div>
                            </div>
                        </a>

                        {{-- Collapsible Body --}}
                        <div class="collapse" id="invoice-{{ $invoice->id }}">
                            <div class="card-body" id="invoice-content-{{ $invoice->id }}">

                                {{-- Invoice Header --}}
                                <div class="mb-3 text-center">
                                    <h5 class="fw-bold">TAX INVOICE</h5>
                                    <p>
                                        <strong>Invoice #:</strong> {{ $invoice->invoice_number }}<br>
                                        @if($invoice->description)
                                            <strong>Description:</strong> {{ $invoice->description }}
                                        @endif
                                    </p>
                                </div>

                                {{-- Supplier & Client Info --}}
                                <div class="d-flex justify-content-between mb-3 flex-wrap">
                                    <div>
                                        <strong>Supplier:</strong><br>
                                        {{ $invoice->supplier->name }}<br>
                                        {{ $invoice->supplier->company }}<br>
                                        {{ $invoice->supplier->email }}<br>
                                        {{ $invoice->supplier->phone }}<br>
                                        @if($invoice->supplier->logo)
                                            <img src="{{ asset('storage/'.$invoice->supplier->logo) }}" alt="Logo" style="max-height:50px;margin-top:5px;">
                                        @endif
                                    </div>
                                    <div>
                                        <strong>Client:</strong><br>
                                        {{ $invoice->lpo->client_name ?? 'N/A' }}<br>
                                        {{ $invoice->lpo->client_company ?? 'N/A' }}<br>
                                        {{ $invoice->lpo->client_address ?? 'N/A' }}
                                    </div>
                                    <div>
                                        <strong>Date:</strong> {{ optional($invoice->invoice_date)->format('d M Y') }}<br>
                                        <strong>LPO #:</strong> {{ $invoice->lpo_id ?? '-' }}<br>
                                        <strong>Due Date:</strong> {{ optional($invoice->due_date)->format('d M Y') ?? 'N/A' }}
                                    </div>
                                </div>

                                {{-- Payment Progress --}}
                                @php
                                    $paidPercent = $invoice->total_amount > 0 ? ($invoice->amount_paid / $invoice->total_amount) * 100 : 0;
                                    $remainingPercent = 100 - $paidPercent;
                                @endphp
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Total: KES {{ number_format($invoice->total_amount,2) }}</small>
                                        <small>Paid: KES {{ number_format($invoice->amount_paid,2) }}</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $paidPercent }}%;"></div>
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $remainingPercent }}%;"></div>
                                    </div>
                                </div>

                                {{-- Items Table --}}
                                <div class="table-responsive mb-2">
                                    <table class="table table-sm table-hover table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Item / Service</th>
                                                <th>Qty / Hours</th>
                                                <th>Unit Price / Rate</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $grandTotal=0; $totalQty=0; @endphp

                                            @if($invoice->lpo && $invoice->lpo->items->count())
                                                @foreach($invoice->lpo->items as $index => $item)
                                                    @php 
                                                        $grandTotal += $item->total;
                                                        $totalQty += $item->quantity;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $index+1 }}</td>
                                                        <td>{{ $item->product }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>KES {{ number_format($item->price,2) }}</td>
                                                        <td>KES {{ number_format($item->total,2) }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td>1</td>
                                                    <td>{{ $invoice->description ?? 'N/A' }}</td>
                                                    <td>1</td>
                                                    <td>KES {{ number_format($invoice->total_amount,2) }}</td>
                                                    <td>KES {{ number_format($invoice->total_amount,2) }}</td>
                                                </tr>
                                                @php
                                                    $grandTotal = $invoice->total_amount;
                                                    $totalQty = 1;
                                                @endphp
                                            @endif
                                        </tbody>
                                        <tfoot class="fw-bold">
                                            <tr>
                                                <td colspan="2">Subtotal</td>
                                                <td>{{ $totalQty }}</td>
                                                <td></td>
                                                <td>KES {{ number_format($grandTotal,2) }}</td>
                                            </tr>
                                            @php $tax = $grandTotal * 0.16; @endphp
                                            <tr>
                                                <td colspan="4">VAT 16%</td>
                                                <td>KES {{ number_format($tax,2) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">Total Amount Due</td>
                                                <td class="fw-bold">KES {{ number_format($grandTotal + $tax,2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                {{-- Payment Details --}}
                                @if($invoice->bank_name || $invoice->mpesa_paybill)
                                <div class="mb-3 p-2 border rounded bg-light">
                                    <strong>Payment Details</strong><br>
                                    @if($invoice->bank_name)
                                        <strong>Bank:</strong> {{ $invoice->bank_name }}<br>
                                    @endif
                                    @if($invoice->account_name)
                                        <strong>Account Name:</strong> {{ $invoice->account_name }}<br>
                                    @endif
                                    @if($invoice->account_number)
                                        <strong>Account Number:</strong> {{ $invoice->account_number }}<br>
                                    @endif
                                    @if($invoice->mpesa_paybill)
                                        <strong>MPESA Paybill:</strong> {{ $invoice->mpesa_paybill }}<br>
                                    @endif
                                    @if($invoice->payment_reference)
                                        <strong>Payment Reference:</strong> {{ $invoice->payment_reference }}
                                    @endif
                                </div>
                                @endif

                                {{-- Payment Terms --}}
                                <div class="mb-2">
                                    <strong>Payment Terms:</strong> Payable within 30 days via Bank Transfer, MPESA, or Cheque.
                                </div>

                                {{-- Actions --}}
                                <div class="d-flex justify-content-between mt-3 flex-wrap">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" data-toggle="dropdown">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            @if($status == 'pending')
                                                <a href="#" class="dropdown-item action-btn" data-action="approve" data-id="{{ $invoice->id }}">
                                                    <i class="fa fa-check text-success mr-2"></i> Approve
                                                </a>
                                                <a href="#" class="dropdown-item text-danger action-btn" data-action="reject" data-id="{{ $invoice->id }}">
                                                    <i class="fa fa-times mr-2"></i> Reject
                                                </a>
                                            @endif
                                            @if($status == 'approved')
                                                <a href="#" class="dropdown-item text-primary action-btn" data-action="paid" data-id="{{ $invoice->id }}">
                                                    <i class="fa fa-money-bill-wave mr-2"></i> Mark Paid
                                                </a>
                                            @endif
                                            @if($invoice->attachment)
                                                <a href="{{ asset('storage/'.$invoice->attachment) }}" target="_blank" class="dropdown-item">
                                                    <i class="fa fa-paperclip mr-2"></i> View Attachment
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <button onclick="printInvoice({{ $invoice->id }})" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-print mr-1"></i> Print
                                    </button>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info rounded">No {{ $status }} invoices found.</div>
                </div>
                @endforelse
            </div>

        </div>
        @endforeach

    </div>

</div>

<script>
$(document).on('click', '.action-btn', function(e){
    e.preventDefault();
    let id = $(this).data('id');
    let action = $(this).data('action');
    if(!confirm('Are you sure?')) return;
    let url = '';
    if(action==='approve') url = '/supplier/invoice/' + id + '/approve';
    if(action==='reject') url = '/supplier/invoice/' + id + '/reject';
    if(action==='paid') url = '/supplier/invoice/' + id + '/paid';
    $.post(url, {_token:'{{ csrf_token() }}'}, function(){ location.reload(); });
});

function printInvoice(id){
    let content = document.getElementById('invoice-content-'+id).innerHTML;
    let mywindow = window.open('', 'PRINT', 'height=650,width=900,top=100,left=150');
    mywindow.document.write('<html><head><title>Invoice</title>');
    mywindow.document.write('<link rel="stylesheet" href="{{ asset("css/app.css") }}">');
    mywindow.document.write('<style>body{font-family:sans-serif;padding:20px;} table{width:100%;border-collapse:collapse;} th, td{padding:5px;text-align:left;} th{background:#f4f4f4;} .fw-bold{font-weight:bold;}</style>');
    mywindow.document.write('</head><body>');
    mywindow.document.write(content);
    mywindow.document.write('</body></html>');
    mywindow.document.close();
    mywindow.focus();
    mywindow.print();
    mywindow.close();
}

$('.collapse').on('show.bs.collapse', function () {
    $(this).prev('a').find('.toggle-icon').text('-');
});
$('.collapse').on('hide.bs.collapse', function () {
    $(this).prev('a').find('.toggle-icon').text('+');
});
</script>

</x-custom-admin-layout>