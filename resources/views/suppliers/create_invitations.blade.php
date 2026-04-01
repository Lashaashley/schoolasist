<x-custom-admin-layout>

<style>
/* Alerts */
.custom-alert{
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 320px;
    z-index: 9999;
    transform: translateX(400px);
    transition: all .4s ease;
}
.custom-alert.show { transform: translateX(0); }

/* Cards */
.card{
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

/* Table header style */
.table-modern th{
    background: #f8f9fa;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
}

/* Badge */
.badge-status{
    font-size: 12px;
    padding: 5px 10px;
    border-radius: 20px;
}

/* LPO Summary */
.lpo-summary{
    background: #f8fafc;
    border-radius: 10px;
    padding: 15px;
    margin-top: 15px;
}

/* Modal table */
#lpoItemsModal .modal-body {
    max-height: 400px;
    overflow-y: auto;
}
</style>

<div class="min-height-200px">
<div class="pd-ltr-20 xs-pd-20-10">

<!-- Alert -->
<div id="status-message" class="alert alert-dismissible fade custom-alert" style="display:none;">
    <strong id="alert-title"></strong>
    <span id="alert-message"></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<h4 class="mb-20">Create Invoice Invitation</h4>

<div class="card">
<form id="create-invitation-form">
@csrf

<div class="row g-3">
    <div class="col-md-4">
        <label>LPO Number</label>
        <select name="lpo_id" id="lpo_id" class="form-control" required>
            <option value="">Select LPO</option>
            @foreach($lpos as $lpo)
                <option value="{{ $lpo->id }}">{{ $lpo->lpo_number }}</option>
            @endforeach
        </select>
        <small class="text-danger" id="lpo_id-error"></small>
    </div>

    <div class="col-md-4">
        <label>Supplier</label>
        <input type="text" id="lpo_supplier" class="form-control" readonly>
        <input type="hidden" name="supplier_id" id="supplier_id">
    </div>

    <div class="col-md-4">
        <label>Category</label>
        <input type="text" id="lpo_category" class="form-control" readonly>
        <input type="hidden" name="category" id="category">
        <small class="text-danger" id="category_id-error"></small>
    </div>
</div>

<!-- Hidden inputs for submission -->
<input type="hidden" name="unit_price" id="unit_price">
<input type="hidden" name="amount" id="amount">

<div class="lpo-summary mt-3">
<div class="row">
    <div class="col-md-4">
        <label>Total Quantity</label>
        <input type="text" id="lpo_quantity" class="form-control" readonly>
    </div>
    <div class="col-md-4">
        <label>Unit Price</label>
        <input type="text" id="lpo_unit_price" class="form-control" readonly>
    </div>
    <div class="col-md-4">
        <label>Items</label>
        <div class="input-group">
            <input type="text" id="lpo_items_product_name" class="form-control" readonly>
            <button type="button" class="btn btn-outline-secondary" id="view-lpo-items">View</button>
        </div>
    </div>
    <div class="col-md-4 mt-2">
        <label>Total Amount</label>
        <input type="text" id="lpo_amount" class="form-control" readonly>
    </div>
    <div class="col-md-4 mt-2">
        <label>Total Items</label>
        <input type="text" id="lpo_items_count" class="form-control" readonly>
    </div>
</div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <label>Message</label>
        <textarea name="message" class="form-control"></textarea>
    </div>
    <div class="col-md-6">
        <label>Expiry Date</label>
        <input type="date" name="expires_at" class="form-control">
    </div>
</div>

<button type="submit" class="btn btn-primary mt-3">
    <i class="fa fa-paper-plane"></i> Send Invitation
</button>

</form>
</div>

<!-- Sent Invitations -->
<div class="card mt-3">
<h5 class="mb-3">Sent Invitations</h5>
<div class="table-responsive">
<table class="table table-hover table-modern align-middle" id="invitations-table">
<thead>
<tr>
<th>Supplier</th>
<th>Category</th>
<th>Items</th>
<th>Quantity</th>
<th>Unit Price</th>
<th>Amount</th>
<th>Message</th>
<th>Expiry</th>
<th>Status</th>
<th>Sent</th>
</tr>
</thead>
<tbody>
@foreach($invitations as $invitation)
<tr id="invitation-row-{{ $invitation->id }}">
<td>{{ $invitation->supplier->name }} - {{ $invitation->supplier->company }}</td>
<td>{{ $invitation->category }}</td>
<td>{{ $invitation->items }}</td>
<td>{{ $invitation->quantity }}</td>
<td>KES {{ number_format($invitation->unit_price,2) }}</td>
<td>KES {{ number_format($invitation->amount,2) }}</td>
<td>{{ $invitation->message }}</td>
<td>{{ optional($invitation->expires_at)->format('d M Y') }}</td>
<td>
<span class="badge-status {{ $invitation->responded ? 'badge-success':'badge-warning' }}">
{{ $invitation->responded ? 'Responded':'Pending' }}
</span>
</td>
<td>{{ $invitation->created_at->format('d M Y H:i') }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>

</div>
</div>

<!-- LPO Items Modal -->
<div class="modal fade" id="lpoItemsModal" tabindex="-1" aria-labelledby="lpoItemsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="lpoItemsModalLabel">LPO Items</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered table-sm" id="modal-lpo-items-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function showAlert(title,message,type='success'){
    $('#alert-title').text(title);
    $('#alert-message').text(message);
    $('#status-message')
        .removeClass('alert-success alert-danger')
        .addClass('alert-'+type)
        .show()
        .addClass('show');
    setTimeout(()=>{ $('#status-message').removeClass('show').fadeOut(); },4000);
}

let currentItems = [];

// Fetch LPO details
$('#lpo_id').change(function(){
    let id = $(this).val();
    $('#lpo_id-error,#category_id-error').text('');
    
    if(!id){
        $('#lpo_supplier, #supplier_id, #lpo_category, #category, #lpo_unit_price, #unit_price, #lpo_quantity, #amount, #lpo_amount, #lpo_items_count, #lpo_items_product_name').val('');
        currentItems = [];
        return;
    }

    $.get('/lpo/'+id+'/details', function(res){
        $('#lpo_supplier').val(res.supplier_name+' - '+res.company);
        $('#supplier_id').val(res.supplier_id);

        let category = res.category_name ?? 'N/A';
        $('#lpo_category').val(category);
        $('#category').val(category);

        $('#lpo_unit_price').val('KES ' + Number(res.unit_price).toLocaleString());
        $('#unit_price').val(res.unit_price);

        $('#lpo_quantity').val(res.quantity);

        let totalAmount = res.quantity * res.unit_price;
        $('#amount').val(totalAmount);
        $('#lpo_amount').val('KES ' + Number(totalAmount).toLocaleString());

        $('#lpo_items_count').val(res.items.length);

        let itemNames = res.items.map(item => item.product);
        $('#lpo_items_product_name').val(itemNames.join(', '));

        currentItems = res.items;
    }).fail(function(){
        showAlert('Error','Failed to fetch LPO details','danger');
    });
});

// Show LPO Items Modal
$('#view-lpo-items').click(function(){
    if(currentItems.length === 0){
        showAlert('Info','No items to display','warning');
        return;
    }

    let tbody = '';
    currentItems.forEach((item,index)=>{
        tbody += `<tr>
<td>${index+1}</td>
<td>${item.product}</td>
<td>${item.quantity}</td>
<td>KES ${Number(item.price).toLocaleString()}</td>
<td>KES ${Number(item.total).toLocaleString()}</td>
</tr>`;
    });

    $('#modal-lpo-items-table tbody').html(tbody);
    new bootstrap.Modal(document.getElementById('lpoItemsModal')).show();
});

// Submit Invitation via AJAX
$('#create-invitation-form').submit(function(e){
    e.preventDefault();
    $.ajax({
        url:"{{ route('supplier.invitations.store') }}",
        method:"POST",
        data:$(this).serialize(),
        success:function(res){
            showAlert('Success', res.message, 'success');
            let i = res.invitation;

            let row = `<tr id="invitation-row-${i.id}">
<td>${i.supplier_name} - ${i.company ?? ''}</td>
<td>${i.category}</td>
<td>${i.items}</td>
<td>${i.quantity}</td>
<td>KES ${Number(i.unit_price).toLocaleString()}</td>
<td>KES ${Number(i.amount).toLocaleString()}</td>
<td>${i.message ?? '-'}</td>
<td>${i.expires_at ?? '-'}</td>
<td><span class="badge-status badge-warning">Pending</span></td>
<td>${i.sent_at ?? '-'}</td>
</tr>`;

            $('#invitations-table tbody').prepend(row);

            $('#create-invitation-form')[0].reset();
            $('#lpo_items_product_name').val('');
            currentItems = [];
        },
        error:function(xhr){
            if(xhr.status === 422){
                let errors = xhr.responseJSON.errors;
                $.each(errors,function(k,v){
                    $('#' + k + '-error').text(v[0]);
                });
            } else {
                showAlert('Error','Failed to send invitation','danger');
            }
        }
    });
});
</script>

</x-custom-admin-layout>