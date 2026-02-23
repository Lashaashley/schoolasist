<x-custom-admin-layout>

<div class="pd-ltr-20">

    <h4 class="mb-20">Supplier Invoices</h4>

    <ul class="nav nav-tabs mb-20">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#pending">Pending</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#approved">Approved</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#paid">Paid</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#rejected">Rejected</a>
        </li>
    </ul>

    <div class="tab-content">

        @foreach(['pending','approved','paid','rejected'] as $status)
        <div class="tab-pane fade {{ $status=='pending'?'show active':'' }}" id="{{ $status }}">
            <div class="card-box pd-20">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Supplier</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th width="250">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices->where('status',$status) as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->supplier->name }}</td>
                            <td>{{ number_format($invoice->amount,2) }}</td>
                            <td>
                                <span class="badge badge-{{ 
                                    $status=='pending'?'warning':
                                    ($status=='approved'?'info':
                                    ($status=='paid'?'success':'danger')) 
                                }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td>
                                @if($status=='pending')
                                    <button class="btn btn-sm btn-success approve-btn"
                                        data-id="{{ $invoice->id }}">Approve</button>
                                    <button class="btn btn-sm btn-danger reject-btn"
                                        data-id="{{ $invoice->id }}">Reject</button>
                                @endif

                                @if($status=='approved')
                                    <button class="btn btn-sm btn-primary pay-btn"
                                        data-id="{{ $invoice->id }}">Mark Paid</button>
                                @endif

                                <a href="{{ asset('storage/'.$invoice->invoice_file) }}" 
                                   class="btn btn-sm btn-secondary" target="_blank">
                                   View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach

    </div>

</div>

<script>
$('.approve-btn').click(function(){
    let id = $(this).data('id');
    $.post('/supplier/invoice/'+id+'/approve',{_token:'{{ csrf_token() }}'},function(){
        location.reload();
    });
});

$('.reject-btn').click(function(){
    let id = $(this).data('id');
    $.post('/supplier/invoice/'+id+'/reject',{_token:'{{ csrf_token() }}'},function(){
        location.reload();
    });
});
</script>

</x-custom-admin-layout>