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
        .custom-alert.show {
            transform: translateX(0);
        }
        .alert-success {
            animation: successPulse 1s ease-in-out;
        }
        @keyframes successPulse {
            0% { transform: scale(0.95); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
    </style>

    <div class="mobile-menu-overlay"></div>
    <div class="min-height-200px">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display: none;">
                <strong id="alert-title"></strong> <span id="alert-message"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

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
                                    <th>Type</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Address</th>
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
                <div class="modal-header">
                    <h5 class="modal-title" id="editSupplierModalLabel">Edit Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit-supplier-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="supplier_id" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name:</label>
                                    <input name="name" id="name" type="text" class="form-control" required>
                                    <small id="name-error" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Company:</label>
                                    <input name="company" id="company" type="text" class="form-control">
                                    <small id="company-error" class="text-danger"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Type:</label>
                                    <select name="type" id="type" class="custom-select form-control">
                                        <option value="">Select Type</option>
                                        <option value="local">Local</option>
                                        <option value="international">International</option>
                                    </select>
                                    <small id="type-error" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email:</label>
                                    <input name="email" id="email" type="email" class="form-control">
                                    <small id="email-error" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Phone:</label>
                                    <input name="phone" id="phone" type="text" class="form-control">
                                    <small id="phone-error" class="text-danger"></small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Address:</label>
                            <input name="address" id="address" type="text" class="form-control">
                            <small id="address-error" class="text-danger"></small>
                        </div>

                        <div class="form-group">
                            <label>Profile Image:</label>
                            <input name="profile" id="profile" type="file" class="form-control" accept="image/*">
                            <small id="profile-error" class="text-danger"></small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" form="edit-supplier-form" class="btn btn-primary">Save Changes</button>
                </div>
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
            var table = $('#suppliers-table').DataTable({
                ajax: "{{ route('suppliers.get') }}",
                columns: [
                    {
                        data: 'profile',
                        render: function(data) {
                            let img = data ? "{{ asset('storage') }}/" + data : "{{ asset('uploads/NO-IMAGE-AVAILABLE.jpg') }}";
                            return `<img src="${img}" width="40" height="40" class="border-radius-100 shadow"/>`;
                        },
                        orderable: false, searchable: false
                    },
                    {data: 'name', name: 'name'},
                    {data: 'company', name: 'company'},
                    {data: 'type', name: 'type'},
                    {data: 'email', name: 'email'},
                    {data: 'phone', name: 'phone'},
                    {data: 'address', name: 'address'},
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            return `
                                <div class="dropdown">
                                    <a class="btn btn-link dropdown-toggle" href="#" data-toggle="dropdown">
                                        <i class="dw dw-more"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item edit-supplier" href="#" data-id="${data}" data-supplier='${encodeURIComponent(JSON.stringify(row))}'>Edit</a>
                                        <a class="dropdown-item delete-supplier" href="#" data-id="${data}">Delete</a>
                                    </div>
                                </div>`;
                        },
                        orderable: false, searchable: false
                    }
                ]
            });

            // Edit supplier
            $(document).on('click', '.edit-supplier', function() {
                let data = JSON.parse(decodeURIComponent($(this).data('supplier')));
                $('#supplier_id').val(data.id);
                $('#name').val(data.name);
                $('#company').val(data.company);
                $('#type').val(data.type);
                $('#email').val(data.email);
                $('#phone').val(data.phone);
                $('#address').val(data.address);
                $('#editSupplierModal').modal('show');
            });

            $('#edit-supplier-form').submit(function(e) {
                e.preventDefault();
                let supplierId = $('#supplier_id').val();
                let formData = new FormData(this);
                $.ajax({
                    url: `{{ url('suppliers') }}/${supplierId}`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    success: function() {
                        $('#editSupplierModal').modal('hide');
                        table.ajax.reload();
                        alert('Supplier updated successfully');
                    },
                    error: function(xhr) {
                        alert('Error updating supplier');
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
                    if(result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('suppliers') }}/${id}`,
                            method: 'DELETE',
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            success: function() {
                                table.ajax.reload();
                                alert('Supplier deleted successfully');
                            }
                        });
                    }
                });
            });
        });
    </script>
</x-custom-admin-layout>