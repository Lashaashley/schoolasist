<x-custom-admin-layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        /* Alerts */
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

        /* Card & Form */
        .card-box {
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.08);
            padding: 30px;
            background: #fff;
        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }

        .form-control {
            border-radius: 8px;
            padding-left: 35px;
            height: 42px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .form-group .input-icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #888;
        }

        /* Payment Section */
        .payment-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-top: 30px;
        }

        .payment-section h6 {
            margin-bottom: 15px;
            font-weight: 700;
            color: #4e73df;
        }

        /* Button */
        button.btn-primary {
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .row > .col-md-4 {
                margin-bottom: 15px;
            }
        }
    </style>

    <div class="mobile-menu-overlay"></div>
    <div class="min-height-200px">
        <div class="pd-ltr-20 xs-pd-20-10">

            <!-- Status Message -->
            <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display: none;">
                <strong id="alert-title"></strong> <span id="alert-message"></span>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>

            <h4 class="mb-3 text-primary fw-bold">Add Supplier</h4>

            <div class="card-box">
                <form id="add-supplier-form" enctype="multipart/form-data">
                    @csrf

                    <!-- Supplier Details -->
                    <div class="row mb-3">
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label>Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                    <input name="name" id="name" type="text" class="form-control" required placeholder="Supplier Name">
                                </div>
                                <small id="name-error" class="text-danger"></small>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label>Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                    <input name="email" id="email" type="email" class="form-control" placeholder="supplier@example.com">
                                </div>
                                <small id="email-error" class="text-danger"></small>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label>Phone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                    <input name="phone" id="phone" type="text" class="form-control" placeholder="+2547XXXXXXXX">
                                </div>
                                <small id="phone-error" class="text-danger"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label>Company</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-building"></i></span>
                                    <input name="company" id="company" type="text" class="form-control" placeholder="Company Name">
                                </div>
                                <small id="company-error" class="text-danger"></small>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label>Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
                                    <input name="address" id="address" type="text" class="form-control" placeholder="Street, City, Postal Code">
                                </div>
                                <small id="address-error" class="text-danger"></small>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label>Profile Image</label>
                                <input name="profile" id="profile" type="file" class="form-control" accept="image/*">
                                <small id="profile-error" class="text-danger"></small>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="payment-section">
                        <h6>Payment Details</h6>

                        <div class="row mb-2">
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group position-relative">
                                    <i class="fa fa-university input-icon"></i>
                                    <input type="text" name="bank_name" class="form-control" placeholder="Bank Name">
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="form-group position-relative">
                                    <i class="fa fa-id-card input-icon"></i>
                                    <input type="text" name="account_name" class="form-control" placeholder="Account Name">
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="form-group position-relative">
                                    <i class="fa fa-hashtag input-icon"></i>
                                    <input type="text" name="account_number" class="form-control" placeholder="Account Number">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-sm-12">
                                <div class="form-group position-relative">
                                    <i class="fa fa-mobile-screen input-icon"></i>
                                    <input type="text" name="mpesa_paybill" class="form-control" placeholder="MPESA Paybill">
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="form-group position-relative">
                                    <i class="fa fa-receipt input-icon"></i>
                                    <input type="text" name="mpesa_till" class="form-control" placeholder="MPESA Till">
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="form-group position-relative">
                                    <i class="fa fa-phone input-icon"></i>
                                    <input type="text" name="mpesa_phone" class="form-control" placeholder="MPESA Phone">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-plus me-1"></i> Add Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#add-supplier-form').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let submitBtn = form.find('button[type="submit"]');
            let originalBtnText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Adding...');
            form.find('small.text-danger').text('');

            let formData = new FormData(this);

            $.ajax({
                url: '{{ route("supplier.store") }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    submitBtn.prop('disabled', false).html(originalBtnText);

                    $('#alert-title').text('Success!');
                    $('#alert-message').text(response.message || 'Supplier added successfully.');
                    $('#status-message').addClass('show').fadeIn();

                    form[0].reset();
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).html(originalBtnText);

                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key + '-error').text(value[0]);
                        });
                    } else {
                        $('#alert-title').text('Error!');
                        $('#alert-message').text(xhr.responseJSON?.message || 'Something went wrong.');
                        $('#status-message').addClass('show').fadeIn();
                    }
                }
            });
        });

        $('.custom-alert .close').on('click', function() {
            $(this).closest('.custom-alert').removeClass('show').fadeOut();
        });
    });
    </script>
</x-custom-admin-layout>