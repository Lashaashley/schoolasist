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
    
    <!-- Make sure CSS is loaded before content -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

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
                        <h4 class="text-blue h4">Parents Management</h4>
                    </div>
                </div>
                
                <div class="card-box pd-20 height-100-p mb-30">
                    <div class="table-responsive">
                        <table class="table table-striped" id="parents-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Surname</th>
                                    <th>Other Names</th>
                                    <th>Type</th>
                                    <th>Workplace</th>
                                    <th>Phone Number</th>
                                    <th>Emergency Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTable will fill this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editParentModal" tabindex="-1" role="dialog" aria-labelledby="editParentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editParentModalLabel">Edit Parent</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-parent-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="text" id="ID" name="id">
                    <div class="row">
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Sir name:</label>
                                <input name="surname" id="surname" type="text" class="form-control wizard-required" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Other name:</label>
                                <input name="othername" id="othername" type="text" class="form-control wizard-required" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Relationship:</label>
                                <select name="typpe" id="typpe" class="custom-select form-control" required autocomplete="off">
                                    <option value="">Select Relationship</option>
                                    <option value="Father">Father</option>
                                    <option value="Mother">Mother</option>
                                    <option value="Guardian">Guardian</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Phone No.:</label>
                                <input name="phoneno" id="phoneno" type="text" class="form-control wizard-required" required autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Email:</label>
                                <input name="email" id="email" type="text" class="form-control wizard-required" required autocomplete="off">
                                <small id="email-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Work Place:</label>
                                <input name="workplace" id="workplace" type="text" class="form-control wizard-required" required autocomplete="off">
                                <small id="workplace-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Emergency Phone no.:</label>
                                <input name="emergencyphone" id="emergencyphone" type="text" class="form-control wizard-required" required autocomplete="off">
                                <small id="emergencyphone-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Address:</label>
                                <input name="address" id="address" type="text" class="form-control wizard-required" required autocomplete="off">
                                <small id="address-error" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="edit-parent-form" class="btn btn-primary">Save Changes</button>
            </div>
        </div>
    </div>
</div>
    
    <!-- Proper order of script loading -->
    <!-- 1. First jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
    <!-- 2. Then DataTables core and styles -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    
    <!-- 3. SweetAlert Scripts -->
    <script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>
    
    <!-- 4. Your custom scripts -->
    <script type="text/javascript">
        $(document).ready(function() {
            // Check if DataTable is loaded
            if ($.fn.DataTable === undefined) {
                console.error('DataTables not loaded! Check script includes.');
                return;
            }

            var table = $('#parents-table').DataTable({
    processing: true,
    serverSide: false,
    ajax: "{{ route('parents.get') }}",
    columns: [
        {data: 'ID', name: 'ID'},
        {data: 'surname', name: 'surname'},
        {data: 'othername', name: 'othername'},
        {data: 'typpe', name: 'typpe'},
        {data: 'workplace', name: 'workplace'},
        {data: 'phoneno', name: 'phoneno'},
        {data: 'emergencyphone', name: 'emergencyphone'},
        {data: 'email', name: 'email'},
        {data: 'address', name: 'address'},
        {
            data: 'id',
            name: 'actions',
            orderable: false,
            searchable: false,
            render: function(data, type, row) {
                return `
                    <div class="dropdown">
                        <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="dw dw-more"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                            <a class="dropdown-item edit-parent" href="javascript:;" data-id="${data}" data-parent="${encodeURIComponent(JSON.stringify(row))}">
                                <i class="dw dw-edit2"></i> Edit
                            </a>
                            <a class="dropdown-item delete-parent" href="javascript:;" data-id="${data}">
                                <i class="dw dw-delete-3"></i> Delete
                            </a>
                        </div>
                    </div>
                `;
            }
        }
    ]
});

// Handle edit button click
$(document).on('click', '.edit-parent', function() {
    var parentData = JSON.parse(decodeURIComponent($(this).data('parent')));
    
    // Populate the modal form with the parent data
    $('#editParentModal #surname').val(parentData.surname);
    $('#editParentModal #othername').val(parentData.othername);
    $('#editParentModal #typpe').val(parentData.typpe);
    $('#editParentModal #phoneno').val(parentData.phoneno);
    $('#editParentModal #email').val(parentData.email);
    $('#editParentModal #workplace').val(parentData.workplace);
    $('#editParentModal #emergencyphone').val(parentData.emergencyphone);
    $('#editParentModal #address').val(parentData.address);
    $('#editParentModal #ID').val(parentData.ID);
    

    
    // Store the parent ID in the form for submission
    $('#edit-parent-form').data('id', parentData.ID);
    
    // Change the form button text
    $('#edit-parent-form button[type="submit"]').text('Update Parent');
    
    // Show the modal
    $('#editParentModal').modal('show');
});

$('#edit-parent-form').on('submit', function(e) {
    e.preventDefault();
    
    var parentId = $(this).data('id');
    var formData = $(this).serialize();
    console.log('Form submitted');
console.log('Parent ID:', parentId);
console.log('Form Data:', formData);
    
    // Add the method spoofing for Laravel
    formData += '&_method=PUT';
    
    $.ajax({
        url: `{{ url('parents') }}/${parentId}`,
        type: 'POST',  // Change back to POST but use method spoofing
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('Success:', response);
            $('#editParentModal').modal('hide');
            table.ajax.reload();
            showAlert('Parent updated successfully');
        },
        error: function(xhr, status, error) {
            console.log('Error:', xhr.responseText);
            // Handle validation errors
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                // Clear previous errors
                $('.error-message').text('');
                $.each(errors, function(key, value) {
                    $('#' + key + '-error').text(value[0]);
                });
            } else {
                showAlert('An error occurred. Please try again.', 'error');
            }
        }
    });
});


            // Delete parent functionality
            $(document).on('click', '.delete-parent', function() {
                var parentId = $(this).data('id');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('parents.destroy', '') }}/" + parentId,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                table.ajax.reload();
                                showAlert('success', 'Success', 'Parent deleted successfully');
                            },
                            error: function(xhr) {
                                showAlert('danger', 'Error', 'Error deleting parent');
                            }
                        });
                    }
                });
            });
        });
        
        function showAlert(type, title, message) {
                const statusMessage = $('#status-message');
                $('#alert-title').html(title);
                $('#alert-message').html(message);
                
                statusMessage
                    .removeClass('alert-success alert-danger')
                    .addClass(`alert-${type}`)
                    .css('display', 'block')
                    .addClass('show');
                
                // Auto hide after 5 seconds if not manually closed
                setTimeout(() => {
                    if (statusMessage.hasClass('show')) {
                        statusMessage.removeClass('show');
                        setTimeout(() => {
                            statusMessage.hide();
                        }, 500);
                    }
                }, 5000);
            }
            $('.close').on('click', function() {
                const alert = $(this).closest('.custom-alert');
                alert.removeClass('show');
                setTimeout(() => {
                    alert.hide();
                }, 500);
            });
    </script>
</x-custom-admin-layout>