<x-custom-admin-layout>
<style>
    .custom-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        z-index: 9999;
        transform: translateX(400px);
        transition: all 0.5s ease;
    }
    .custom-alert.show { transform: translateX(0); }
    .alert-success { animation: successPulse 1s ease-in-out; }
    @keyframes successPulse {
        0% { transform: scale(0.95); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }
    .table-responsive { overflow-x: auto; }
</style>

<div class="mobile-menu-overlay"></div>
<div class="min-height-200px">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="pd-20 card-box mb-30">
            <div class="clearfix mb-20">
                <div class="pull-left">
                    <h4 class="text-blue h4">Suppliers Management</h4>
                </div>
            </div>

            <div class="card-box pd-20 height-100-p mb-30">
                <div class="table-responsive">
                    <table class="table table-striped" id="suppliers-table">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Bank Name</th>
                                <th>Account Name</th>
                                <th>Account Number</th>
                                <th>Mpesa Paybill</th>
                                <th>Mpesa Till</th>
                                <th>Mpesa Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog" aria-labelledby="editSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="edit-supplier-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="supplier_id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Name</label>
                            <input name="name" id="name" type="text" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Company</label>
                            <input name="company" id="company" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Email</label>
                            <input name="email" id="email" type="email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Phone</label>
                            <input name="phone" id="phone" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="mt-2">
                        <label>Address</label>
                        <input name="address" id="address" type="text" class="form-control">
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <label>Bank Name</label>
                            <input name="bank_name" id="bank_name" type="text" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Account Name</label>
                            <input name="account_name" id="account_name" type="text" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Account Number</label>
                            <input name="account_number" id="account_number" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <label>Mpesa Paybill</label>
                            <input name="mpesa_paybill" id="mpesa_paybill" type="text" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Mpesa Till</label>
                            <input name="mpesa_till" id="mpesa_till" type="text" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Mpesa Phone</label>
                            <input name="mpesa_phone" id="mpesa_phone" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="mt-2">
                        <label>Profile Image</label>
                        <input name="profile" id="profile" type="file" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>

<script>
$(document).ready(function() {
    let table = $('#suppliers-table').DataTable({
        ajax: "{{ route('suppliers.get') }}",
        columns: [
            { data: 'profile', render: function(data) {
                let img = data ? "{{ asset('storage') }}/" + data : "{{ asset('uploads/NO-IMAGE-AVAILABLE.jpg') }}";
                return `<img src="${img}" width="40" height="40" class="rounded-circle shadow"/>`;
            }},
            { data: 'name' },
            { data: 'company' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'address' },
            { data: 'bank_name' },
            { data: 'account_name' },
            { data: 'account_number' },
            { data: 'mpesa_paybill' },
            { data: 'mpesa_till' },
            { data: 'mpesa_phone' },
            { 
                data: null,
                render: function(data, type, row) {
                    return `
                        <a href="#" class="btn btn-sm btn-primary edit-supplier" data-id="${row.id}">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger delete-supplier" data-id="${row.id}">Delete</a>`;
                },
                orderable:false,
                searchable:false
            }
        ],
        scrollX: true
    });

    // Open edit modal
    $(document).on('click', '.edit-supplier', function() {
        let id = $(this).data('id');
        $.get(`/suppliers/${id}`, function(data) {
            $('#supplier_id').val(data.id);
            $('#name').val(data.name);
            $('#company').val(data.company);
            $('#email').val(data.email);
            $('#phone').val(data.phone);
            $('#address').val(data.address);
            $('#bank_name').val(data.bank_name);
            $('#account_name').val(data.account_name);
            $('#account_number').val(data.account_number);
            $('#mpesa_paybill').val(data.mpesa_paybill);
            $('#mpesa_till').val(data.mpesa_till);
            $('#mpesa_phone').val(data.mpesa_phone);
            $('#editSupplierModal').modal('show');
        });
    });

    // Submit edit form
    $('#edit-supplier-form').submit(function(e) {
        e.preventDefault();
        let supplierId = $('#supplier_id').val();
        let formData = new FormData(this);
        formData.append('_method','PUT');

        $.ajax({
            url: `/suppliers/${supplierId}`,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function() {
                $('#editSupplierModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Success','Supplier updated successfully','success');
            },
            error: function(xhr) {
                let msg = 'Failed to update supplier';
                if(xhr.responseJSON && xhr.responseJSON.errors){
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // Delete supplier
    $(document).on('click', '.delete-supplier', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if(result.isConfirmed){
                $.ajax({
                    url: `/suppliers/${id}`,
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function() {
                        table.ajax.reload();
                        Swal.fire('Deleted','Supplier deleted successfully','success');
                    }
                });
            }
        });
    });
});
</script>
</x-custom-admin-layout>