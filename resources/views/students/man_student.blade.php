<x-custom-admin-layout>
   <style>
     .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: var(--modal-shadow);
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            overflow: hidden;
        }

        .modal-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem 2rem;
            border: none;
            position: relative;
        }

        .modal-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.3) 100%);
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.4rem;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .modal-title i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
           	.tab-container {
    display: flex;
    border-bottom: 1px solid #ccc;
    margin-bottom: 20px;
}

.tab-button {
    background-color: #f8f9fa;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 10px 20px;
    font-size: 12.5px;
    transition: background-color 0.3s;
}

.tab-button:hover {
    background-color: #e9ecef;
}

.tab-button.active {
    font-weight: bold;
    color: #7360ff;
    background-color: #fff;
    border-bottom: 3px solid #7360ff; /* Hide border bottom when active */
}

.tab-content {
    display: none;
    padding: 20px;
}

.tab-content.active {
    display: block;
}
.action-buttons {
            padding: 1px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-enhanced {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-draft {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1);
            color: white;
        }
        
        .btn-finalize {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .btn-cancel {
            background: linear-gradient(135deg, #e93a04ff, #d62f05ff);
            color: white;
        }  
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
        .toggle-container {
  display: flex;
  align-items: center;
  position: relative;
}

.gender-toggle-switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
  margin: 0 10px;
}

.gender-toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.gender-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #3498db; /* Blue for male (default) */
  transition: .4s;
}

.gender-slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
}

input:checked + .gender-slider {
  background-color:rgb(249, 24, 222); /* Pink for female */
}

input:focus + .gender-slider {
  box-shadow: 0 0 1pxrgb(12, 101, 245);
}

input:checked + .gender-slider:before {
  transform: translateX(26px);
}

.gender-slider.round {
  border-radius: 34px;
}

.gender-slider.round:before {
  border-radius: 50%;
}

.toggle-label {
  font-weight: 500;
  transition: opacity 0.3s ease;
}

.male-label {
  opacity: 1;
}

.female-label {
  opacity: 0.3;
}

/* Boarding Toggle Styles */
.boarding-toggle-switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 30px;
}

.boarding-toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.boarding-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color:rgb(236, 13, 35); /* Red for off state */
  transition: .4s;
}

.boarding-slider-button {
  position: absolute;
  height: 22px;
  width: 22px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  z-index: 2;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

input:checked + .boarding-slider {
  background-color: #28a745; /* Green for on state */
}

input:focus + .boarding-slider {
  box-shadow: 0 0 1px #28a745;
}

input:checked + .boarding-slider .boarding-slider-button {
  transform: translateX(30px);
}

.boarding-slider.round {
  border-radius: 34px;
}

.boarding-toggle-icon {
  font-size: 12px;
  line-height: 1;
}

.boarding-toggle-on {
  color: #28a745;
  display: none;
}

.boarding-toggle-off {
  color:rgb(235, 14, 36);
}

input:checked ~ .boarding-slider .boarding-toggle-on {
  display: block;
}

input:checked ~ .boarding-slider .boarding-toggle-off {
  display: none;
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

            <div class="card-box mb-30">
                
                
                <div class="pb-20 px-20">
                    <table id="agents-table" class="data-table table stripe hover nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th class="table-plus">Full Name</th>
                                <th>Adm No</th>
                                <th>ST ID</th>
                                <th>Gender</th>
                                <th>Adm Date</th>
                                <th>Class</th>
                                <th>State</th>
                                
                                <th class="datatable-nosort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editstaffModal" tabindex="-1" role="dialog" aria-labelledby="edituserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edituserModalLabel">Edit Student</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
             <form method="post" name="staffForm" id="staffForm" enctype="multipart/form-data" >
                            @csrf
                             <input name="stid" id="stid" type="text" class="form-control wizard-required" required="true" autocomplete="off" required hidden>
                            <div class="modal-body">
                            <div class="row">
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label >Campus:</label>
                                        <select name="caid" id="campus" class="custom-select form-control" required>

                                        </select>
                                        <small id="caid-error" class="text-danger"></small>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label >Class:</label>
                                        <select name="claid" id="claid" class="custom-select form-control" required>

                                        </select>
                                        <small id="claid-error" class="text-danger"></small>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label >Stream:</label>
                                        <select name="stream" id="stream" class="custom-select form-control" required>

                                        </select>
                                        <small id="stream-error" class="text-danger"></small>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label >Sir name:</label>
                                        <input name="sirname" id="sirname" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label >Other name:</label>
                                        <input name="othername" id="othername" type="text" class="form-control wizard-required" required="true" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label >ADM NO:</label>
                                        <input name="admno" id="admno" class="form-control wizard-required" required readonly>
                                        <small id="admno-error" class="text-danger"></small>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label >Date of birth:</label>
                                        <input name="dateob" id="dateob" type="text" class="form-control date-picker" required="true" autocomplete="off" required>
                                        <small id="dateob-error" class="text-danger"></small>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label >Addmission date:</label>
                                        <input name="admdate" id="admdate" type="text" class="form-control date-picker" required="true" autocomplete="off" required>
                                        <small id="admdate-error" class="text-danger"></small>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label >Gender:</label>
                                        <label class="d-flex align-items-center">
                                            <div class="toggle-container d-flex align-items-center">
                                                <span class="toggle-label male-label">Male</span>
                                                <div class="gender-toggle-switch">
                                                    <input type="hidden" name="gender" id="genderInput" value="male">
                                                    <input type="checkbox"  id="genderToggle" class="gender-toggle-input">
                                                    <span class="gender-slider round"></span>
                                                </div>
                                                <span class="toggle-label female-label">Female</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 col-sm-12">
                                    <div class="form-group">
                                        <label >Boarder:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text p-0 bg-transparent border-0">
                                                    <span class="boarding-toggle-switch mb-0 mr-0">
                                                        <input type="hidden" name="border" id="borderInput" value="no">
                                                        <input type="checkbox" id="boardingToggle">
                                                        <span class="boarding-slider round">
                                                            <span class="boarding-slider-button">
                                                                <span class="boarding-toggle-icon boarding-toggle-on">✓</span>
                                                                <span class="boarding-toggle-icon boarding-toggle-off">✖</span>
                                                            </span>
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label>House:</label>
                                        <select name="houseid" id="house" class="custom-select form-control" disabled>
                                            <!-- options will be added dynamically or exist here -->
                                        </select>
                                        <small id="houseid-error" class="text-danger"></small>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label >Parent/ Gurdian:</label>
                                        <select id="parent" name="parent" class="form-control" style="width: 100%;">
                                            <option value="">Select Parent</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-enhanced btn-draft">
                                <i class="fas fa-user-check"></i>Update Student
                            </button>
                            </div>
                        </form>
        </div>
    </div>
</div>



    
    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script> 
        const amanage = '{{ route("students.data") }}';
        const branches = '{{ route("branches.getDropdown") }}';
        const parents = '{{ route("parents.getDropdown") }}';
        const getuser = '{{ route("get.student", ":id") }}';
        const classes = '{{ route("classes.getDropdown") }}';
        const streams = '{{ route("streams.getDropdown") }}';
        const houses = '{{ route("houses.getDropdown") }}';

        var DEFAULT_IMAGE_URL = "{{ asset('uploads/NO-IMAGE-AVAILABLE.jpg') }}";
        
  
        
       

    </script>
    <script src="{{ asset('js/smanage.js') }}"></script>
    
    <script> 
      $(document).ready(function() {
         $('#staffForm').on('submit', function (e) {
    e.preventDefault();
    
    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    const id = $('#stid').val();
    const formData = new FormData(this);

    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
    
    formData.append('_method', 'POST');
    
    $.ajax({ 
        url: `{{ url('student') }}/${id}`,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            showAlert('success', 'Success!', response.message);
            $('#editstaffModal').modal('hide');
            
            // Optionally reload the data table or refresh the page
            if (typeof table !== 'undefined') {
                table.ajax.reload();
            }
        },
        error: function (xhr) {
            console.error('Error response:', xhr.responseJSON);
            
            if (xhr.status === 422) {
                // Validation errors
                let errors = xhr.responseJSON.errors;
                
                $.each(errors, function (key, messages) {
                    // Find the input field
                    let input = $(`[name="${key}"]`);
                    
                    // Add error class
                    input.addClass('is-invalid');
                    
                    // Add error message
                    input.after(`<div class="invalid-feedback d-block">${messages[0]}</div>`);
                });
                
                showAlert('danger', 'Validation Error!', 'Please check the form for errors.');
            } else if (xhr.status === 404) {
                showAlert('danger', 'Error!', 'Agent not found.');
            } else {
                let errorMessage = xhr.responseJSON?.message || 'Error updating agent.';
                showAlert('danger', 'Error!', errorMessage);
            }
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
});
 
                  
        $('#campus').on('change', function() {
          const selectedCampusId = $(this).val();
          if (selectedCampusId) {
            loadClassesByCampus(selectedCampusId);
            loadHousesByCampus(selectedCampusId);
          } else {
            // Clear classes dropdown if no campus is selected
          const classDropdown = $('#claid');
          classDropdown.empty();
          classDropdown.append('<option value="">Select Class</option>');

          const classDropdown1 = $('#house');
          classDropdown1.empty();
          classDropdown1.append('<option value="">Select House</option>');

          const classDropdown2 = $('#stream');
          classDropdown1.empty();
          classDropdown1.append('<option value="">Select Stream</option>');
        }
        
      });
       $('#claid').on('change', function() {
          const selectedCampusId = $(this).val();
          if (selectedCampusId) {
            loadstreamsByclass(selectedCampusId);
           
          } else {
            // Clear classes dropdown if no campus is selected
          const classDropdown = $('#stream');
          classDropdown.empty();
          classDropdown.append('<option value="">Select Stream</option>');

         
        }
        
      });
      function loadClassesByCampus(campusId) {
        $.ajax({
          url: "{{ route('classes.getByCampus') }}",
          type: "GET",
          data: { campusId: campusId },
          success: function (response) {
            const dropdown = $('#claid');
            dropdown.empty();
            dropdown.append('<option value="">Select Class</option>');
            response.data.forEach(function (classes) {
              dropdown.append(
                `<option value="${classes.ID}">${classes.claname}</option>`
              );
            });
          },
          error: function () {
            alert('Failed to load classes. Please try again.');
          }
        });
      }
      function loadstreamsByclass(campusId) {
        $.ajax({
          url: "{{ route('streams.getByclass') }}",
          type: "GET",
          data: { campusId: campusId },
          success: function (response) {
            const dropdown = $('#stream');
            dropdown.empty();
            dropdown.append('<option value="">Select stream</option>');
            response.data.forEach(function (streams) {
              dropdown.append(
                `<option value="${streams.ID}">${streams.strmname}</option>`
              );
            });
          },
          error: function () {
            alert('Failed to load strmname. Please try again.');
          }
        });
      }
      function loadHousesByCampus(campusId) {
        $.ajax({
          url: "{{ route('houses.getByCampus') }}",
          type: "GET",
          data: { campusId: campusId },
          success: function (response) {
            const dropdown = $('#house');
            dropdown.empty();
            dropdown.append('<option value="">Select House</option>');
            response.data.forEach(function (house) {
              dropdown.append(
                `<option value="${house.ID}">${house.housen}</option>`
              );
            });
          },
          error: function () {
            alert('Failed to load classes. Please try again.');
          }
        });
      }
     });
       
    </script>
    
   
</x-custom-admin-layout>