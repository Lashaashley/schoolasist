<x-custom-admin-layout>

<div class="pd-ltr-20">

    <h4 class="mb-20">Supplier Invoices</h4>

    <ul class="nav nav-tabs mb-20">
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
            <div class="card-box pd-20">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Supplier</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th width="120" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices->where('status',$status) as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->supplier->name }}</td>
                            <td>{{ number_format($invoice->total_amount,2) }}</td>
                            <td>
                                <span class="badge badge-{{ 
                                    $status=='pending'?'warning':
                                    ($status=='approved'?'info':
                                    ($status=='paid'?'success':'danger')) 
                                }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>

                            <!-- ACTION DROPDOWN -->
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm"
                                            type="button"
                                            data-toggle="dropdown">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right">

                                        {{-- Pending Actions --}}
                                        @if($status == 'pending')
                                            <a href="#"
                                               class="dropdown-item action-btn"
                                               data-action="approve"
                                               data-id="{{ $invoice->id }}">
                                                Approve
                                            </a>

                                            <a href="#"
                                               class="dropdown-item text-danger action-btn"
                                               data-action="reject"
                                               data-id="{{ $invoice->id }}">
                                                Reject
                                            </a>
                                        @endif

                                        {{-- Approved Actions --}}
                                        @if($status == 'approved')
                                            <a href="#"
                                               class="dropdown-item text-primary action-btn"
                                               data-action="paid"
                                               data-id="{{ $invoice->id }}">
                                                Mark Paid
                                            </a>
                                        @endif

                                        {{-- View --}}
                                        @if($invoice->attachment)
                                            <a href="{{ asset('storage/'.$invoice->attachment) }}"
                                               target="_blank"
                                               class="dropdown-item">
                                                View
                                            </a>
                                        @endif

                                    </div>
                                </div>
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
$(document).on('click', '.action-btn', function(e){
    e.preventDefault();

    let id = $(this).data('id');
    let action = $(this).data('action');
    let url = '';

    if(action === 'approve'){
        url = '/supplier/invoice/' + id + '/approve';
    }

    if(action === 'reject'){
        url = '/supplier/invoice/' + id + '/reject';
    }

    if(action === 'paid'){
        url = '/supplier/invoice/' + id + '/paid';
    }

    if(!confirm('Are you sure?')) return;

    $.post(url, {_token: '{{ csrf_token() }}'}, function(){
        location.reload();
    });
});
</script>

</x-custom-admin-layout>