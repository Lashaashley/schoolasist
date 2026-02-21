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
     /* Gender Toggle Styles */
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
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
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
            <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -40px;">
              <div class="row align-items-center">
                <div class="col-md-12">
                  
                </div>
              </div>
            </div>
            
            <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
                <form id="add-student-form" enctype="multipart/form-data">
                    @csrf
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
                                <select name="claid" id="class" class="custom-select form-control" required>

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
                                        <i class="fas fa-user-check"></i>Add Student
                                    </button>
                </form>
            </div>
        </div>
    </div>
    <!---<script src="{{ asset('js/custom-dropdown.js') }}"></script>--->
    <script>
      document.addEventListener('DOMContentLoaded', function () {
  // Gender toggle
  const genderToggle = document.getElementById('genderToggle');
  const genderInput = document.getElementById('genderInput');
  const maleLabel = document.querySelector('.male-label');
  const femaleLabel = document.querySelector('.female-label');

  const updateGenderLabels = () => {
    if (genderToggle.checked) {
      maleLabel.style.opacity = '0.3';
      femaleLabel.style.opacity = '1';
      genderInput.value = 'female';
    } else {
      maleLabel.style.opacity = '1';
      femaleLabel.style.opacity = '0.3';
      genderInput.value = 'male';
    }
  };

  updateGenderLabels();
  genderToggle.addEventListener('change', updateGenderLabels);

  const boardingToggle = document.getElementById('boardingToggle');
const borderInput = document.getElementById('borderInput');
const houseSelect = document.getElementById('house');

const updateBorderInput = () => {
    const isChecked = boardingToggle.checked;

    borderInput.value = isChecked ? 'yes' : 'no';

    if (isChecked) {
        houseSelect.removeAttribute('disabled');
        houseSelect.setAttribute('required', 'required');
    } else {
        houseSelect.setAttribute('disabled', 'disabled');
        houseSelect.removeAttribute('required');
    }
};

updateBorderInput(); // Run on load
boardingToggle.addEventListener('change', updateBorderInput);

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

            $('#add-student-form').on('submit', function(e) {
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('students.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showAlert('success', 'Success!', response.message);
                        $('#add-student-form')[0].reset();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                      submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $.ajax({
                url: "{{ route('branches.getDropdown') }}",
                type: "GET",
                success: function (response) {
                    const dropdown = $('#campus');
                    dropdown.empty();
                    dropdown.append('<option value="">Select campus</option>');
                    response.data.forEach(function (branch) {
                        dropdown.append(
                            `<option value="${branch.ID}">${branch.branchname}</option>`
                        );
                    });
                },
                error: function () {
                    alert('Failed to load streams. Please try again.');
                },
            });
            // Add this after your existing campus dropdown AJAX code
            $('#campus').on('change', function() {
            const campusId = $(this).val();
            // Target specifically the first card-box
            const $statsCardBox = $('.card-box.pd-20.height-100-p.mb-30').first();
            if (!campusId) {
            // Clear the stats box if no campus is selected
            $statsCardBox.html('<div class="row align-items-center"><div class="col-md-12"></div></div>');
            return;
            }
            // Show loading spinner
            $statsCardBox.html(`
            <div class="d-flex justify-content-center align-items-center" style="height: 100px;">
            <div class="spinner-border text-primary spinner-border-sm" role="status">
            <span class="sr-only">Loading...</span>
            </div>
            </div>
            `);
            $.ajax({
              url: "{{ route('campus.stats') }}",
              type: "GET",
              data: {
                campus_id: campusId
              },
              success: function(response) {
                const branchName = $('#campus option:selected').text();
                // Generate the admission number: first letter of branch name + (latest student ID + 1)
                const firstLetter = branchName.charAt(0).toUpperCase();
                const nextStudentId = (response.latest_student_id + 1);
                const admissionNumber = firstLetter + nextStudentId;
                // Set the generated admission number in the input field
                $('#admno').val(admissionNumber);
                let statsHtml = `
                <div class="row align-items-center">
                  <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <h5 class="mb-0 font-14"><i class="icon-copy dw dw-analytics-5 mr-1"></i>Campus Overview</h5>
                        <span class="badge badge-success font-10">Active Campus</span>
                    </div>
                    <!-- Stats Summary -->
                      <div class="row mb-2">
                        <div class="col-md-6">
                          <div class="stat-box p-1 bg-light-blue rounded shadow-sm">
                            <div class="row no-gutters">
                              <div class="col-6">
                                <div class="d-flex align-items-center p-1">
                                  <div class="icon-box bg-primary rounded-circle mr-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                    <i class="icon-copy dw dw-home text-white" style="font-size: 12px;"></i>
                                  </div>
                                <div>
                                <p class="mb-0 font-12">${response.house_count} Houses</p>
                              </div>
                            </div>
                          </div>
                          <div class="col-6">
                            <div class="d-flex align-items-center p-1">
                              <div class="icon-box bg-success rounded-circle mr-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                <i class="icon-copy dw dw-open-book text-white" style="font-size: 12px;"></i>
                              </div>
                            <div>
                            <p class="mb-0 font-12">${response.class_count} Classes</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="stat-box p-1 bg-light-orange rounded shadow-sm">
                    <div class="d-flex align-items-center">
                      <div class="icon-box bg-warning rounded-circle mr-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                        <i class="icon-copy dw dw-group text-white" style="font-size: 12px;"></i>
                      </div>
                    <div>
                    <p class="mb-0 font-12">${response.total_students} Total Students</p>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <!-- Houses & Classes -->
            <div class="row">
              <div class="col-md-6">
                <div class="card card-box mb-2 shadow-sm" style="border-radius: 3px; border-left: 3px solid #2b5797;">
                  <div class="card-header bg-transparent py-1 px-2 border-bottom-0">
                    <h6 class="mb-0 font-12">
                      <i class="icon-copy dw dw-home-1 mr-1"></i> Houses
                      <span class="badge badge-primary badge-pill float-right font-10">${response.house_count}</span>
                    </h6>
                  </div>
                <div class="card-body p-0" style="max-height: 150px; overflow-y: auto;">`;
                // Create two-column layout for house stats
                if (response.house_stats.length > 0) {
                statsHtml += `<div class="row no-gutters">`;
                // Process houses in two columns, 5 rows each
                for (let i = 0; i < Math.min(10, response.house_stats.length); i++) {
                  const house = response.house_stats[i];
                  const colClass = i % 2 === 0 ? 'col-6 border-right' : 'col-6';
                  statsHtml += `
                  <div class="${colClass}">
                  <div class="py-1 px-2 d-flex justify-content-between align-items-center ${i < 8 ? 'border-bottom' : ''}">
                  <span class="font-11 text-truncate" style="max-width: 70%;">${house.name}</span>
                  <span class="badge badge-info badge-pill font-10">${house.student_count}</span>
                  </div>
                  </div>`;
                  if (i === 9 && response.house_stats.length > 10) {
                    statsHtml += `
                    </div>
                    <div class="row no-gutters">`;
                  }
                }
                // If we have an odd number of houses, add an empty cell to balance the layout
                if (Math.min(10, response.house_stats.length) % 2 !== 0) {
                  statsHtml += `<div class="col-6"></div>`;
                }
                statsHtml += `</div>`;
                // If there are more houses, add a "View All" link
                if (response.house_stats.length > 10) {
                  statsHtml += `
                  <div class="text-center py-1 border-top">
                  <a href="#" class="font-10 text-primary">View all ${response.house_stats.length} houses</a>
                  </div>`;
                }
              } else {
                statsHtml += `
                <div class="text-center py-1">
                <span class="font-11 text-muted">No houses found</span>
                </div>`;
              }
              statsHtml += `
              </div>
              </div>
              </div>
              <div class="col-md-6">
              <div class="card card-box mb-2 shadow-sm" style="border-radius: 3px; border-left: 3px solid #008299;">
              <div class="card-header bg-transparent py-1 px-2 border-bottom-0">
              <h6 class="mb-0 font-12">
              <i class="icon-copy dw dw-open-book mr-1"></i> Classes
              <span class="badge badge-success badge-pill float-right font-10">${response.class_count}</span>
              </h6>
              </div>
              <div class="card-body p-0" style="max-height: 150px; overflow-y: auto;">`;
              // Create two-column layout for class stats
              if (response.class_stats.length > 0) {
                statsHtml += `<div class="row no-gutters">`;
                // Process classes in two columns, 5 rows each
                for (let i = 0; i < Math.min(10, response.class_stats.length); i++) {
                  const classItem = response.class_stats[i];
                  const colClass = i % 2 === 0 ? 'col-6 border-right' : 'col-6';
                  statsHtml += `
                  <div class="${colClass}">
                  <div class="py-1 px-2 d-flex justify-content-between align-items-center ${i < 8 ? 'border-bottom' : ''}">
                  <span class="font-11 text-truncate" style="max-width: 70%;">${classItem.name}</span>
                  <span class="badge badge-success badge-pill font-10">${classItem.student_count}</span>
                  </div>
                  </div>`;
                  // After every 10 items, start a new row block
                  if (i === 9 && response.class_stats.length > 10) {
                    statsHtml += `
                    </div>
                    <div class="row no-gutters">`;
                  }
                }
                // If we have an odd number of classes, add an empty cell to balance the layout
                if (Math.min(10, response.class_stats.length) % 2 !== 0) {
                  statsHtml += `<div class="col-6"></div>`;
                }
                statsHtml += `</div>`;
                // If there are more classes, add a "View All" link
                if (response.class_stats.length > 10) {
                  statsHtml += `
                  <div class="text-center py-1 border-top">
                  <a href="#" class="font-10 text-primary">View all ${response.class_stats.length} classes</a>
                  </div>`;
                }
              } else {
                statsHtml += `
                <div class="text-center py-1">
                <span class="font-11 text-muted">No classes found</span>
                </div>`;
              }
              statsHtml += `
              </div>
              </div>
              </div>
              </div>
              </div>
              </div>
              `;
              // Update only the first card box with the statistics
              $statsCardBox.html(statsHtml);
            },
            error: function(xhr) {
            console.error('Error fetching campus statistics', xhr);
            // Show error message in the card box
            $statsCardBox.html(`
                <div class="alert alert-danger py-1 px-2" role="alert">
                    <i class="icon-copy dw dw-warning mr-1"></i>
                    <span class="font-11">Failed to load campus statistics</span>
                </div>
            `);
          }
        });
      });
            $.ajax({
              url: "{{ route('parents.getDropdown') }}",
              type: "GET",
              success: function (response) {
                const dropdown = $('#parent');
                dropdown.empty();
                dropdown.append('<option value="">Select Parent</option>');
                response.data.forEach(function (parent) {
                  dropdown.append(
                    `<option value="${parent.ID}">${parent.parentname}</option>`
                  );
                });
                dropdown.select2({
                  placeholder: "Select a parent",
                  allowClear: true,
                  width: '100%'
                });
              },
              error: function () {
                alert('Failed to load parents. Please try again.');
              },
            });

        });
        $('#campus').on('change', function() {
          const selectedCampusId = $(this).val();
          if (selectedCampusId) {
            loadClassesByCampus(selectedCampusId);
            loadHousesByCampus(selectedCampusId);
          } else {
            // Clear classes dropdown if no campus is selected
          const classDropdown = $('#class');
          classDropdown.empty();
          classDropdown.append('<option value="">Select Class</option>');

          const classDropdown1 = $('#house');
          classDropdown1.empty();
          classDropdown1.append('<option value="">Select House</option>');
        }
        
      });
       $('#class').on('change', function() {
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
            const dropdown = $('#class');
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
    </script>
</x-custom-admin-layout>