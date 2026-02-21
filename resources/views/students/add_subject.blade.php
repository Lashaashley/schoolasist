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
            <h6 style="margin-top: -40px;">Create Subject</h6>
            <div class="card-box pd-20 height-100-p mb-30" >
                <form id="add-subject-form" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                         <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label >Subject Name:</label>
                                <input name="sname" id="sname" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label >Subject Code:</label>
                                <input name="scode" id="scode" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                                <small id="scode-error" class="text-danger"></small>
                            </div>
                        </div>
                       <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                 <label >Department:</label>
                               <select id="sdept" name="sdept" class="form-control" >
                                  <option value="">Select Department</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label >Mandatory to classes:</label>
                                <div class="checkbox-container flex">
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" id="isall" name="isall" value="Yes" class="form-check-input">
                                        <label for="isall" class="form-check-label">Mandatory</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                       
                        
                        
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Create Subject</button>
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

            $('#add-subject-form').on('submit', function(e) {
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('subject.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showAlert('success', 'Success!', response.message);
                        $('#add-subject-form')[0].reset();
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

            $.ajax({
        url: "{{ route('depts.getDepts') }}",
        type: "GET",
        success: function (response) {
            const dropdown = $('#sdept');
            dropdown.empty();
            dropdown.append('<option value="">Select Department</option>');
            response.data.forEach(function (depts) {
                dropdown.append(
                    `<option value="${depts.ID}">${depts.deptname}</option>`
                );
            });
        },
        error: function () {
            alert('Failed to load streams. Please try again.');
        },
    });
          

        });
       
        
      
    </script>
</x-custom-admin-layout>