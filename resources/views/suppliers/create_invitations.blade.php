<x-custom-admin-layout>

<style>
/* Alerts */
.custom-alert {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 320px;
    z-index: 9999;
    transform: translateX(400px);
    transition: all 0.4s ease;
}
.custom-alert.show { transform: translateX(0); }

/* Cards */
.card {
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

/* Table */
.table-modern th {
    background:#f8f9fa;
    font-weight:600;
    font-size:13px;
    text-transform:uppercase;
}
.table-modern td, .table-modern th {
    vertical-align: middle !important;
}
.badge-status { font-size:12px; padding:5px 10px; border-radius:20px; }
</style>

<div class="min-height-200px">
    <div class="pd-ltr-20 xs-pd-20-10">

        <!-- Alert -->
        <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display:none;">
            <strong id="alert-title"></strong>
            <span id="alert-message"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <h4 class="mb-20">Create Invoice Invitation</h4>

        <!-- Invitation Form Card -->
        <div class="card">
            <form id="create-invitation-form">
                @csrf
                <div class="row g-3">

                    <div class="col-md-4">
                        <label>Supplier</label>
                        <select name="supplier_id" class="form-control" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">
                                    {{ $supplier->name }} - {{ $supplier->company }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="supplier_id-error"></small>
                    </div>

                <!--<div class="col-md-4">
                        <label>Invoice</label>
                        <select name="invoice_id" class="form-control" required>
                            <option value="">Select Invoice</option>
                            @foreach($approvedInvoices ?? [] as $invoice)
                                <option value="{{ $invoice->id }}">
                                    {{ $invoice->invoice_number }} - ${{ number_format($invoice->balance, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="invoice_id-error"></small>
                    </div> -->

                    <div class="col-md-4">
                        <label>Category</label>
                        <select name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            <option value="stationery">Stationery & Office Supplies</option>
                            <option value="furniture">Furniture & Equipment</option>
                            <option value="uniforms">Uniforms & Clothing</option>
                            <option value="food">Food & Catering</option>
                            <option value="books">Books & Educational Materials</option>
                            <option value="it">IT & Electronics</option>
                            <option value="cleaning">Cleaning & Sanitation Supplies</option>
                            <option value="transport">Transport & Logistics</option>
                            <option value="sports">Sports & Recreation</option>
                            <option value="maintenance">Maintenance & Repairs</option>
                            <option value="subscriptions">Stationery Subscriptions / Periodicals</option>
                            <option value="medical">Medical & Safety Supplies</option>
                        </select>
                        <small class="text-danger" id="category-error"></small>
                    </div>

                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <label>Message (Optional)</label>
                        <textarea name="message" class="form-control" rows="2"></textarea>
                        <small class="text-danger" id="message-error"></small>
                    </div>
                    <div class="col-md-6">
                        <label>Expiry Date</label>
                        <input type="date" name="expires_at" class="form-control">
                        <small class="text-danger" id="expires_at-error"></small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fa fa-paper-plane"></i> Send Invitation
                </button>
            </form>
        </div>

        <!-- Invitations Table Card -->
        <div class="card">
            <h5 class="mb-3">Sent Invitations</h5>
            <div class="table-responsive">
                <table class="table table-hover table-modern align-middle" id="invitations-table">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Invoice</th>
                            <th>Category</th>
                            <th>Message</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Sent At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invitations as $invitation)
                        <tr id="invitation-row-{{ $invitation->id }}">
                            <td>{{ $invitation->supplier->name }} - {{ $invitation->supplier->company }}</td>
                            <td>{{ $invitation->invoice->invoice_number ?? '-' }}</td>
                            <td>{{ $invitation->category }}</td>
                            <td>{{ $invitation->message ?? '-' }}</td>
                            <td>{{ optional($invitation->expires_at)->format('d M Y') ?? '-' }}</td>
                            <td>
                                <span class="badge-status {{ $invitation->responded ? 'badge-success' : 'badge-warning' }}">
                                    {{ $invitation->responded ? 'Responded' : 'Pending' }}
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    function showAlert(title, message, type='success') {
        $('#alert-title').text(title);
        $('#alert-message').text(message);
        $('#status-message').removeClass('alert-success alert-danger alert-warning')
            .addClass('alert-' + type)
            .show()
            .addClass('show');
        setTimeout(() => { $('#status-message').removeClass('show').fadeOut(); }, 4000);
    }

    $('#create-invitation-form').submit(function(e){
        e.preventDefault();
        $(this).find('small.text-danger').text('');

        $.ajax({
            url: "{{ route('supplier.invitations.store') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function(res){
                $('#create-invitation-form').trigger('reset');
                showAlert('Success', res.message, 'success');

                let invitation = res.invitation;
                let statusClass = invitation.responded ? 'badge-success' : 'badge-warning';
                let statusText = invitation.responded ? 'Responded' : 'Pending';

                let newRow = `
                    <tr id="invitation-row-${invitation.id}">
                        <td>${invitation.supplier_name} - ${invitation.supplier_company ?? '-'}</td>
                        <td>${invitation.invoice_number ?? '-'}</td>
                        <td>${invitation.category}</td>
                        <td>${invitation.message ?? '-'}</td>
                        <td>${invitation.expires_at}</td>
                        <td><span class="badge-status ${statusClass}">${statusText}</span></td>
                        <td>${invitation.sent_at}</td>
                    </tr>
                `;
                $('#invitations-table tbody').prepend(newRow);
            },
            error: function(xhr){
                if(xhr.status === 422){
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value){
                        $('#' + key + '-error').text(value[0]);
                    });
                } else {
                    showAlert('Error', 'Something went wrong.', 'danger');
                }
            }
        });
    });

});
</script>

</x-custom-admin-layout>