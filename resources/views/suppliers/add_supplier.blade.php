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

            <h6 style="margin-top: -40px;">Add Supplier</h6>
            <div class="card-box pd-20 height-100-p mb-30">
                <form id="add-supplier-form" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Name:</label>
                                <input name="name" id="name" type="text" class="form-control" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Email:</label>
                                <input name="email" id="email" type="email" class="form-control" autocomplete="off">
                                <small id="email-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Phone:</label>
                                <input name="phone" id="phone" type="text" class="form-control" autocomplete="off">
                                <small id="phone-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Company:</label>
                                <input name="company" id="company" type="text" class="form-control" autocomplete="off">
                                <small id="company-error" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Address:</label>
                                <input name="address" id="address" type="text" class="form-control" autocomplete="off">
                                <small id="address-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label>Profile Image:</label>
                                <input name="profile" id="profile" type="file" class="form-control" accept="image/*">
                                <small id="profile-error" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Supplier</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            function showAlert(type, title, message) {
                const statusMessage = $('#status-message');
                $('#alert-title').html(title);
                $('#alert-message').html(message);

                statusMessage
                    .removeClass('alert-success alert-danger')
                    .addClass(`alert-${type}`)
                    .css('display', 'block')
                    .addClass('show');

                setTimeout(() => {
                    if (statusMessage.hasClass('show')) {
                        statusMessage.removeClass('show');
                        setTimeout(() => statusMessage.hide(), 500);
                    }
                }, 5000);
            }

            $('.close').on('click', function() {
                const alert = $(this).closest('.custom-alert');
                alert.removeClass('show');
                setTimeout(() => alert.hide(), 500);
            });

            $('#add-supplier-form').on('submit', function(e) {
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

                $.ajax({
                    url: "{{ route('supplier.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showAlert('success', 'Success!', response.message + (response.invoice_no ? ' Invoice: ' + response.invoice_no : ''));
                        $('#add-supplier-form')[0].reset();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding supplier');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });

        });
    </script>
</x-custom-admin-layout>