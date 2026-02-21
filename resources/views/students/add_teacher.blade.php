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
            <h6 style="margin-top: -40px;">Add Teacher</h6>
            <div class="card-box pd-20 height-100-p mb-30" >
                <form id="add-teacher-form" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                         <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label >First name:</label>
                                <input name="fname" id="fname" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label >Sir name:</label>
                                <input name="surname" id="surname" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                            </div>
                        </div>
                       <div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label >Staff No.:</label>
                                <input name="workno" id="workno" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                                <small id="workno-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label >Teacher type:</label>
                                <select name="trtype" id="trtype" class="custom-select form-control" required="true" autocomplete="off">
                                  <option value="">Select Type</option>
                                  <option value="normal">Normal</option>
                                  <option value="HOD">HOD</option>
                            
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12">
                            <div class="form-group">
                                <label >Gender:</label>
                                <select name="gender" id="gender" class="custom-select form-control" required="true" autocomplete="off">
                                  <option value="">Select Gender</option>
                                  <option value="male">Male</option>
                                  <option value="female">Female</option>
                            
                                </select>
                            </div>
                        </div>
                        
                        
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label > Email:</label>
                                <input name="email" id="email" type="email" class="form-control wizard-required"  autocomplete="off">
                                <small id="email-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label > Phone No.:</label>
                                <input name="phoneno" id="phoneno" type="text" class="form-control wizard-required"  autocomplete="off" required>
                                <small id="phoneno-error" class="text-danger"></small>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label > Date employed:</label>
                                <input name="dateemployed" id="dateemployed" type="date" class="form-control wizard-required" autocomplete="off">
                                <small id="dateemployed-error" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                           <div class="form-group">
                                                <label>Profile pic:</label>
                                                <div class="custom-file">
                                                    <input name="profile" id="profile" type="file" class="custom-file-input" accept=".png,.jpg,.jpeg" onchange="validateFile('profile')">
                                                    <label class="custom-file-label" for="file" id="selector">Upload</label>
                                                    <span class="text-danger" id="file-error"></span>
                                                </div>
                                            </div>
                        </div>
                        
                    </div>
                    <button type="submit" class="btn btn-primary">Add Teacher</button>
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

            $('#add-teacher-form').on('submit', function(e) {
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('Teacher.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showAlert('success', 'Success!', response.message);
                        $('#add-teacher-form')[0].reset();
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
        function validateFile(inputId) {
    const fileInput = document.getElementById(inputId);
    const file = fileInput.files[0];
    const allowedTypes = ['image/png', 'image/jpeg'];
    const maxSize = 2 * 1024 * 1024; // 2 MB

    if (!allowedTypes.includes(file.type)) {
        alert('Only PNG and JPEG files are allowed.');
        fileInput.value = ''; // Reset the input
        return false;
    }

    if (file.size > maxSize) {
        alert('File size should not exceed 2 MB.');
        fileInput.value = ''; // Reset the input
        return false;
    }
    return true;
}
        
      
    </script>
</x-custom-admin-layout>