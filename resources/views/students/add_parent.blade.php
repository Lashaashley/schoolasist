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
            <h6 style="margin-top: -40px;">Add Parent</h6>
            <div class="card-box pd-20 height-100-p mb-30" >
                <form id="add-parent-form" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label >Sir name:</label>
                                <input name="surname" id="surname" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label >Other name:</label>
                                <input name="othername" id="othername" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label >Relationship:</label>
                                <select name="typpe" id="typpe" class="custom-select form-control" required="true" autocomplete="off">
                                  <option value="">Select Relationship</option>
                                  <option value="Father">Father</option>
                                  <option value="Mother">Mother</option>
                                  <option value="Guardian">Guardian</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label >Phone No.:</label>
                                <input name="phoneno" id="phoneno" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label > Email:</label>
                                <input name="email" id="email" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                                <small id="email-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label > Work Place:</label>
                                <input name="workplace" id="workplace" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                                <small id="workplace-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label >Emergency Phone no.:</label>
                                <input name="emergencyphone" id="emergencyphone" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                                <small id="emergencyphone-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label >Address.:</label>
                                <input name="address" id="address" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                                <small id="address-error" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Parent</button>
                </form>
            </div>
        </div>
    </div>
    <!---<script src="{{ asset('js/custom-dropdown.js') }}"></script>--->
    
    <script>
      document.addEventListener('DOMContentLoaded', function () {
  
});

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

            $('#add-parent-form').on('submit', function(e) {
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('Parent.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showAlert('success', 'Success!', response.message);
                        $('#add-parent-form')[0].reset();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding Parent');
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