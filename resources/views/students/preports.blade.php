<x-custom-admin-layout>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .custom-alert {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    z-index: 9999;
    opacity: 0;
    transform: translateX(400px);
    transition: all 0.5s ease;
    display: none; /* Initially hidden via JS, but transition handled by opacity/transform */
}

.custom-alert.show {
    opacity: 1;
    transform: translateX(0);
    display: block; /* Needed to make it visible */
}

.alert-success {
    animation: successPulse 1s ease-in-out;
}

@keyframes successPulse {
    0% { transform: scale(0.95); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}
        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 8px;
            color: #667eea;
        }
        .form-select, .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: var(--transition);
            background: white;
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
    font-size: 14px;
    transition: background-color 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tab-button:hover {
    background-color: #e9ecef;
}

.tab-button.active {
    font-weight: bold;
    color: #7360ff;
    background-color: #fff;
    border-bottom: 3px solid #7360ff;
}

.tab-button i {
    color: #667eea;
    font-size: 16px;
    transition: color 0.3s;
}

.tab-button.active i {
    color: #7360ff;
}

.tab-content {
    display: none;
    padding: 20px;
}

.tab-content.active {
    display: block;
}
.header-container{
    font-size: 0.5rem;
    display: flex;
    justify-content: center; /* Center horizontally */
    align-items: center;

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
            background: linear-gradient(135deg, #ffc107, #ff8c00);
            color: white;
        }   

        #reportformmodal .modal-dialog {
    max-width: 800px;
    width: 90%;
}
  #modaltermly .modal-dialog {
    max-width: 900px;
    width: 90%;
}
  #modalcoverall .modal-dialog {
    max-width: 900px;
    width: 90%;
}
  #modalanalysis .modal-dialog {
    max-width: 900px;
    width: 90%;
}
       
    </style>

    <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert">
    <strong id="alert-title"></strong> <span id="alert-message"></span>
    <button type="button" class="close" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif


    <div class="mobile-menu-overlay"></div>
    
    <div class="min-height-200px"style="margin-top: -20px;">
        <h1 class="header-container">Reports Query</h1>
        <div class="tab-container">
            <button class="tab-button active" onclick="openTab(event, 'studentpa')">
                <i class="fas fa-user-graduate"></i> Student Performance
            </button>
            <button class="tab-button" id="streamper-tab" onclick="openTab(event, 'streamper')">
                <i class="fas fa-chart-line"></i> Stream Performance
            </button>
            <button class="tab-button" id="classper-tab" onclick="openTab(event, 'classper')">
                <i class="fas fa-users"></i> Class Performance
            </button>
        </div>
        <div id="studentpa" class="tab-content active" style="margin-top: -20px;">
            <div class="card-box pd-20 height-100-p mb-30">
                <h5 class="text-center mb-1">Report Form</h5>
                <div class="row mb-2">
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fas fa-chalkboard-teacher"></i>
                            Class:
                        </label>
                        <select name="classlist" id="classlist" class="form-select" required>

                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="icon-copy dw dw-user1 mr-2"></i>
                            Student:
                        </label>
                        <select name="studentlist" id="studentlist" class="form-select" required>

                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fa fa-file-text-o"></i>
                            Exam:
                        </label>
                        <select name="ExamList" id="ExamList" class="form-select" required>

                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class=""></i>
                        </label>
                        <button type="button" class="btn btn-enhanced btn-draft" id="preview-report" data-toggle="modal" data-target="#reportformmodal">
                            <i class="fas fa-eye"></i>
                            View
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-box pd-20 height-100-p mb-30">
                <h5 class="text-center mb-1">Termly</h5>
                <div class="row mb-2">
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fas fa-chalkboard-teacher"></i>
                            Term:
                        </label>
                        <select name="termlist" id="termlist" class="form-select" required>

                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="icon-copy dw dw-user1 mr-2"></i>
                            Student:
                        </label>
                        <select name="pstudentlist" id="pstudentlist" class="form-select" required>

                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class=""></i>
                        </label>
                        <button type="button" class="btn btn-enhanced btn-draft" id="viewtermly" data-toggle="modal" data-target="#modaltermly">
                            <i class="fas fa-eye"></i>
                            View
                        </button>
                    </div>
                </div>
            </div>

        </div>
        <div id="classper" class="tab-content" style="margin-top: -20px;">
            <div class="card-box pd-20 height-100-p mb-30">
                <h5 class="text-center mb-1">Class Broadsheet Report</h5>
                <div class="row mb-2">
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fas fa-chalkboard-teacher"></i>
                            Class:
                        </label>
                        <select name="pclasslist" id="pclasslist" class="form-select" required>

                        </select>
                        
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fa fa-file-text-o"></i>
                            Exam:
                        </label>
                        <select name="pExamList" id="pExamList" class="form-select" required>

                        </select>
                        
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class=""></i>
                        </label>
                        <button type="button" class="btn btn-enhanced btn-draft" id="coverall" data-toggle="modal" data-target="#modalcoverall">
                            <i class="fas fa-eye"></i>
                            View
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-box pd-20 height-100-p mb-30">
                <h5 class="text-center mb-1">Class Analysis</h5>
                <div class="row mb-2">
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fas fa-chalkboard-teacher"></i>
                            Class:
                        </label>
                        <select name="aclasslist" id="aclasslist" class="form-select" required>

                        </select>
                        
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="fa fa-file-text-o"></i>
                            Exam:
                        </label>
                        <select name="aExamList" id="aExamList" class="form-select" required>

                        </select>
                        
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class=""></i>
                        </label>
                        <button type="button" class="btn btn-enhanced btn-draft" id="classanalysis" data-toggle="modal" data-target="#modalanalysis">
                            <i class="fas fa-eye"></i>
                            View
                        </button>
                    </div>
                </div>
            </div>

        </div>
       
    </div>
    
    <div class="modal fade" id="modaltermly" tabindex="-1" aria-labelledby="modaltermlyLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modaltermlyLabel">
                    <i class="icon-copy dw dw-print mr-2"></i>
                    Termly Performance
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
           <div class="modal-body">
    <!-- Loading indicator -->
    <div id="print-loading" class="text-center" style="display: none;">
        <div class="spinner-border" role="status">
            <span class="sr-only">Generating PDF...</span>
        </div>
        <p class="mt-2">Preparing Report...</p>
    </div>

    <!-- Error message -->
    <div id="print-error" class="alert alert-danger" style="display: none;">
        <i class="icon-copy dw dw-warning"></i>
        <span id="print-error-message"></span>
    </div>

    <!-- PDF viewer will be injected here -->
</div>
<div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="icon-copy dw dw-cancel"></i>
                    Cancel
                </button>
                <!--<button type="button" class="btn btn-info" id="preview-terml">
                    <i class="icon-copy dw dw-eye"></i>
                    Preview
                </button>-->
                <button type="button" class="btn btn-primary" id="download-report">
                    <i class="icon-copy dw dw-download"></i>
                    Download PDF
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalcoverall" tabindex="-1" aria-labelledby="modalcoverall" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalcoverallLabel">
                    <i class="icon-copy dw dw-print mr-2"></i>
                    Termly Performance
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="print-loadingoverall" class="text-center" style="display: none;">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Generating PDF...</span>
                    </div>
                    <p class="mt-2">Preparing Report...</p>
                </div>
                <div id="print-error" class="alert alert-danger" style="display: none;">
                    <i class="icon-copy dw dw-warning"></i>
                    <span id="print-error-messageover"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="icon-copy dw dw-cancel"></i>
                    Cancel
                </button>
               <!-- <button type="button" class="btn btn-info" id="preview-terml">
                    <i class="icon-copy dw dw-eye"></i>
                    Preview
                </button>-->
                <button type="button" class="btn btn-primary" id="download-report">
                    <i class="icon-copy dw dw-download"></i>
                    Download PDF
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalanalysis" tabindex="-1" aria-labelledby="modalanalysis" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalanalysisLabel">
                    <i class="icon-copy dw dw-print mr-2"></i>
                    Termly Performance
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="print-modalanalysis" class="text-center" style="display: none;">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Generating PDF...</span>
                    </div>
                    <p class="mt-2">Preparing Report...</p>
                </div>
                <div id="print-error" class="alert alert-danger" style="display: none;">
                    <i class="icon-copy dw dw-warning"></i>
                    <span id="print-error-messageover"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="icon-copy dw dw-cancel"></i>
                    Cancel
                </button>
                <!--<button type="button" class="btn btn-info" id="preview-terml">
                    <i class="icon-copy dw dw-eye"></i>
                    Preview
                </button>-->
                <button type="button" class="btn btn-primary" id="download-report">
                    <i class="icon-copy dw dw-download"></i>
                    Download PDF
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reportformmodal" tabindex="-1" aria-labelledby="reportformmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportformmodalLabel">
                    <i class="icon-copy dw dw-print mr-2"></i>
                    Print Student Statement
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
           <div class="modal-body">
    <!-- Loading indicator -->
    <div id="print-loading" class="text-center" style="display: none;">
        <div class="spinner-border" role="status">
            <span class="sr-only">Generating PDF...</span>
        </div>
        <p class="mt-2">Preparing Report...</p>
    </div>

    <!-- Error message -->
    <div id="print-error" class="alert alert-danger" style="display: none;">
        <i class="icon-copy dw dw-warning"></i>
        <span id="print-error-message"></span>
    </div>

    <!-- PDF viewer will be injected here -->
</div>
<div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="icon-copy dw dw-cancel"></i>
                    Cancel
                </button>
                <!--<button type="button" class="btn btn-info" >
                    <i class="icon-copy dw dw-eye"></i>
                    Preview
                </button>-->
                <button type="button" class="btn btn-primary" id="download-report">
                    <i class="icon-copy dw dw-download"></i>
                    Download PDF
                </button>
            </div>
        </div>
    </div>
</div>
                

    

<script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>

<!--<script src="{{ asset('js/custom-dropdown.js') }}"></script>--->
<script src="{{ asset('src/plugins/sweetalert2/sweet-alert.init.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>


// Input validation and real-time feedback




// Enhanced button interactions
$(document).ready(function() { 
  
  $('#classper-tab').on('click', function() {
    $.ajax({
    url: "{{ route('classes.getDropdown') }}",
    type: "GET",
    success: function (response) {
        const dropdown = $('#pclasslist');
        const dropdown2 = $('#aclasslist');
        dropdown.empty();
        dropdown2.empty();
        dropdown.append('<option value="">Select class</option>');
        dropdown2.append('<option value="">Select class</option>');
        response.data.forEach(function (classes) {
            dropdown.append(
                `<option value="${classes.ID}">${classes.claname}</option>`
            );
            dropdown2.append(
                `<option value="${classes.ID}">${classes.claname}</option>`
            );
        });
    },
    error: function () {
        alert('Failed to load Classes. Please try again.');
    },
});

$.ajax({
    url: "{{ route('periods.current') }}",
    type: "GET",
    success: function (response) {
        if (response.status === 'success') {
            currentPeriod = response.periodname;
        } else {
            currentPeriod = 'Unknown Period';
        }

        // After fetching period, fetch exams
        $.ajax({
            url: "{{ route('exam.getDropdown') }}",
            type: "GET",
            success: function (response) {
                const dropdown = $('#pExamList');
                const dropdown2 = $('#aExamList');
                dropdown.empty();
                dropdown.append('<option value="">Select Exam</option>');
                dropdown2.empty();
                dropdown2.append('<option value="">Select Exam</option>');
                response.data.forEach(function (exam) {
                    dropdown.append(
                        `<option value="${exam.examname} - ${currentPeriod}">
                            ${exam.examname} - ${currentPeriod}
                         </option>`
                    );
                    dropdown2.append(
                        `<option value="${exam.examname} - ${currentPeriod}">
                            ${exam.examname} - ${currentPeriod}
                         </option>`
                    );
                });
            },
            error: function () {
                alert('Failed to load exams. Please try again.');
            },
        });
    },
    error: function () {
        alert('Failed to load period info.');
    }
});

            });


    

});
$.ajax({
    url: "{{ route('classes.getDropdown') }}",
    type: "GET",
    success: function (response) {
        const dropdown = $('#classlist');
        dropdown.empty();
        dropdown.append('<option value="">Select class</option>');
        response.data.forEach(function (classes) {
            dropdown.append(
                `<option value="${classes.ID}">${classes.claname}</option>`
            );
        });
    },
    error: function () {
        alert('Failed to load Classes. Please try again.');
    },
});
$.ajax({
    url: "{{ route('periods.getDropdown') }}",
    type: "GET",
    success: function (response) {
        const dropdown = $('#termlist');
        dropdown.empty();
        dropdown.append('<option value="">Select period</option>');
        response.data.forEach(function (periods) {
            dropdown.append(
                `<option value="${periods.examperiod}">${periods.periodname}</option>`
            );
        });
    },
    error: function () {
        alert('Failed to load Periods. Please try again.');
    },
});
function getstudents(selectedclassId) {
            $.ajax({
                url: "{{ route('billing.getstudents2') }}",
                type: "GET",
                data: { selectedclassId: selectedclassId },
                success: function(response) {
                    const dropdown = $('#studentlist');
                    dropdown.empty();
                    dropdown.append('<option value="">Select student</option>');
                    if (Array.isArray(response)) {
                        response.forEach(function(student) {
                            dropdown.append(
                                `<option value="${student.admno}">${student.admno} - ${student.studentname}</option>`
                            );
                        });
                        dropdown.select2({
                            placeholder: "Select Student",
                            allowClear: true,
                            width: '100%'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    alert('Failed to load students. Please try again.');
                }
            });
        }
        function getpstudents(selectedperiod) {
            $.ajax({
                url: "{{ route('terms.getstudents') }}",
                type: "GET",
                data: { selectedperiod: selectedperiod },
                success: function(response) {
                    const dropdown = $('#pstudentlist');
                    dropdown.empty();
                    dropdown.append('<option value="">Select student</option>');
                    response.data.forEach(function(student) {
                        dropdown.append(
                            `<option value="${student.admno}">${student.admno} - ${student.studentname}</option>`
                        );
                    });
                    dropdown.select2({
                        placeholder: "Select Student",
                        allowClear: true,
                        width: '100%'
                    });
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    alert('Failed to load students. Please try again.');
                }
            });
        }

        $('#classlist').on('change', function() {
                const selectedclassId = $(this).val();
                if (selectedclassId) {
                    getstudents(selectedclassId);
                    //clearBillingDetails();
                } else {
                    const classDropdown = $('#studentlist');
                    classDropdown.empty();
                    classDropdown.append('<option value="">Select Student</option>');
                }
            });
            $('#termlist').on('change', function() {
                const selectedperiod = $(this).val();
                if (selectedperiod) {
                    getpstudents(selectedperiod);
                    //clearBillingDetails();
                } else {
                    const classDropdown = $('#pstudentlist');
                    classDropdown.empty();
                    classDropdown.append('<option value="">Select Student</option>');
                }
            });
            let currentPeriod = '';

$.ajax({
    url: "{{ route('periods.current') }}",
    type: "GET",
    success: function (response) {
        if (response.status === 'success') {
            currentPeriod = response.periodname;
        } else {
            currentPeriod = 'Unknown Period';
        }

        // After fetching period, fetch exams
        $.ajax({
            url: "{{ route('exam.getDropdown') }}",
            type: "GET",
            success: function (response) {
                const dropdown = $('#ExamList');
                dropdown.empty();
                dropdown.append('<option value="">Select Exam</option>');
                response.data.forEach(function (exam) {
                    dropdown.append(
                        `<option value="${exam.examname} - ${currentPeriod}">
                            ${exam.examname} - ${currentPeriod}
                         </option>`
                    );
                });
            },
            error: function () {
                alert('Failed to load exams. Please try again.');
            },
        });
    },
    error: function () {
        alert('Failed to load period info.');
    }
});
       /* $.ajax({
    url: "{{ route('subject.get') }}",
    type: "GET",
    success: function (response) {
        const dropdown = $('#subjectlist');
        dropdown.empty();
        dropdown.append('<option value="">Select Subject</option>');
        response.data.forEach(function (teachers) {
            dropdown.append(
                `<option value="${teachers.ID}">${teachers.sname}</option>`
            );
        });
    },
    error: function () {
        alert('Failed to load Classes. Please try again.');
    },
});

let currentPeriod = '';

$.ajax({
    url: "{{ route('periods.current') }}",
    type: "GET",
    success: function (response) {
        if (response.status === 'success') {
            currentPeriod = response.periodname;
        } else {
            currentPeriod = 'Unknown Period';
        }

        // After fetching period, fetch exams
        $.ajax({
            url: "{{ route('exam.getDropdown') }}",
            type: "GET",
            success: function (response) {
                const dropdown = $('#ExamList');
                dropdown.empty();
                dropdown.append('<option value="">Select Exam</option>');
                response.data.forEach(function (exam) {
                    dropdown.append(
                        `<option value="${exam.examname} - ${currentPeriod}">
                            ${exam.examname} - ${currentPeriod}
                         </option>`
                    );
                });
            },
            error: function () {
                alert('Failed to load exams. Please try again.');
            },
        });
    },
    error: function () {
        alert('Failed to load period info.');
    }
});*/

$('#reportformmodal').on('show.bs.modal', function (event) {
    // Get current student information
    var selectedOption = $('#studentlist option:selected');
    var admno = selectedOption.val();
    var studentText = selectedOption.text();
    
    if (admno && studentText !== 'Select Student') {
        // Parse student information (assuming format: "ADMNO - Name (Class)")
        var parts = studentText.split(' - ');
        var name = parts.length > 1 ? parts[1].split(' (')[0] : 'N/A';
        var classPart = studentText.match(/\(([^)]+)\)/);
        var className = classPart ? classPart[1] : 'N/A';
        
        $('#student-admno').text(admno);
        $('#student-name').text(name);
        $('#student-class').text(className);
        
        // Store current admission number for printing
        $('#reportformmodal').data('current-admno', admno);
        
        // Reset form
        resetPrintForm();
    } else {
        showPrintError('Please select a student first');
    }
});

$('#preview-report').on('click', function () {

    
    var admno = $('#studentlist').val();
    let examtype = $('#ExamList').val();

    if (!admno) {
        showPrintError('No student selected');
        return;
    }

    showPrintLoading();
   
    $.ajax({
        url: '{{ route("preview.student.report") }}',
        method: 'POST',
        xhrFields: {
            responseType: 'blob' // This ensures we get binary PDF data
        },
        data: {
            admno: admno,
            examtype: examtype,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            hidePrintLoading();

            var blob = new Blob([data], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);

            var pdfViewer = `
                <iframe src="${url}" width="100%" height="500px" style="border: none;"></iframe>
            `;

            $('#reportformmodal .modal-body').html(pdfViewer);
        },
        error: function (xhr) {
            hidePrintLoading();
            showPrintError('Failed to generate statement. Please try again.');
        }
    });
});
$('#coverall').on('click', function () {

    
    var classid = $('#pclasslist').val();
    let exam = $('#pExamList').val();

    if (!classid) {
        showPrintError('No Class selected');
        return;
    }

    showPrintLoadingreci();
   
    $.ajax({
        url: '{{ route("preview.classperf.reports") }}',
        method: 'POST',
        xhrFields: {
            responseType: 'blob' // This ensures we get binary PDF data
        },
        data: {
            classid: classid,
            exam: exam,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            hidePrintLoadingreci();

            var blob = new Blob([data], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);

            var pdfViewer = `
                <iframe src="${url}" width="100%" height="500px" style="border: none;"></iframe>
            `;

            $('#modalcoverall .modal-body').html(pdfViewer);
        },
        error: function (xhr) {
            hidePrintLoadingreci();
            showPrintError('Failed to generate statement. Please try again.');
        }
    });
});
$('#classanalysis').on('click', function () {

    
    var classid = $('#aclasslist').val();
    let exam = $('#aExamList').val();

    if (!classid) {
        showPrintError('No Class selected');
        return;
    }   

    showPrintLoadinganal();
   
    $.ajax({
        url: '{{ route("preview.classanal.reports") }}',
        method: 'POST',
        xhrFields: {
            responseType: 'blob' // This ensures we get binary PDF data
        },
        data: {
            classid: classid,
            exam: exam,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) { 
            hidePrintLoadinganal();

            var blob = new Blob([data], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);

            var pdfViewer = `
                <iframe src="${url}#toolbar=0&navpanes=0&scrollbar=0" width="100%" height="500px" style="border: none;"></iframe>
            `;

            $('#modalanalysis .modal-body').html(pdfViewer);
        },
        error: function (xhr) {
            hidePrintLoadinganal();
            showPrintError('Failed to generate statement. Please try again.');
        }
    });
});
$('#viewtermly').on('click', function () { 

    
    var admno = $('#pstudentlist').val();
    let examperiod = $('#termlist').val();

    if (!admno) {
        showPrintError('No student selected');
        return;
    }

    showPrintLoading();
   
    $.ajax({
        url: '{{ route("preview.termly.reports") }}',
        method: 'POST',
        xhrFields: {
            responseType: 'blob' // This ensures we get binary PDF data
        },
        data: {
            admno: admno,
            examperiod: examperiod,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            hidePrintLoading();

            var blob = new Blob([data], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);

            var pdfViewer = `
                <iframe src="${url}" width="100%" height="500px" style="border: none;"></iframe>
            `;

            $('#modaltermly .modal-body').html(pdfViewer);
        },
        error: function (xhr) {
            hidePrintLoading();
            showPrintError('Failed to generate statement. Please try again.');
        }
    });
});
     function openTab(evt, tabName) { 
    var i, tabContent, tabButton;

    // Hide all tab content
    tabContent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabContent.length; i++) {
        tabContent[i].style.display = "none";
    }

    // Remove the "active" class from all tab buttons
    tabButton = document.getElementsByClassName("tab-button");
    for (i = 0; i < tabButton.length; i++) {
        tabButton[i].className = tabButton[i].className.replace(" active", "");
    }

    // Show the current tab and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}     
function showPrintLoading() {
    $('#print-loading').show();
    $('#print-options').hide();
    $('#print-error').hide();
    $('#preview-statement, #download-statement').prop('disabled', true);
}
function showPrintLoadingreci() {
    $('#print-loadingoverall').show();
    $('#print-optionsreci').hide();
    $('#print-errorover').hide();
    
}
function showPrintLoadinganal() {
    $('#print-modalanalysis').show();
    $('#print-optionsreci').hide();
    $('#print-errorover').hide();
    
}
function hidePrintLoading() {
    $('#print-loading').hide();
    $('#print-options').show();
    $('#preview-statement, #download-statement').prop('disabled', false);
}
function hidePrintLoadingreci() {
    $('#print-loadingoverall').hide();
    $('#print-optionsreci').show();
   
}
function hidePrintLoadinganal() {
    $('#print-modalanalysisl').hide();
    $('#print-optionsreci').show();
   
}

function showPrintError(message) {
    $('#print-error-message').text(message);
    $('#print-error').show();
    $('#print-loading').hide();
    $('#print-options').show();
    $('#preview-statement, #download-statement').prop('disabled', false);
}

function resetPrintForm() {
    $('#print-error').hide();
    $('#print-loading').hide();
    $('#print-options').show();
    $('#pdf-format').val('A4');
    $('#pdf-orientation').val('portrait');
    $('#include-summary').prop('checked', true);
    $('#include-school-header').prop('checked', true);
    $('#preview-statement, #download-statement').prop('disabled', false);
}      
           function showAlert(type, title, message) {
    const statusMessage = $('#status-message');
    $('#alert-title').html(title);
    $('#alert-message').html(message);

    // Reset and show the alert with styles
    statusMessage
        .removeClass('alert-success alert-danger alert-warning alert-info') // Reset all types
        .addClass(`alert-${type}`)
        .css('display', 'block') // Make it block but still off-screen (opacity:0)
        .addClass('show'); // Slide it in and fade it in (opacity:1)

    // Auto hide after 5 seconds if not manually closed
    setTimeout(() => {
        if (statusMessage.hasClass('show')) {
            closeAlert(); // Use the centralized close function
        }
    }, 5000);
}

// Centralized function to close the alert with animation
function closeAlert() {
    const alert = $('#status-message');
    alert.removeClass('show'); // This triggers the slide-out/fade-out transition

    // Wait for the transition to finish before hiding it completely
    alert.one('transitionend webkitTransitionEnd oTransitionEnd', function() {
        if (!alert.hasClass('show')) { // Double-check it's still closed
            alert.hide();
        }
    });
}

// Use event delegation for the close button
$(document).on('click', '#status-message .close', function() {
    closeAlert();
});
    </script>
</x-custom-admin-layout>
