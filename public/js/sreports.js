  $(document).ready
  (function() {
            // Initialize DataTable with server-side processing
            var table = $('#agents-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: amanage,
                    type: 'GET',
                    error: function(xhr, error, thrown) {
                        console.error('DataTable Ajax error:', error);
                        showAlert('danger', 'Error!', 'Error loading data');
                    }
                },
                columns: [
                    {
                        data: null,
                        orderable: true,
                        render: function(data, type, row) {
                            return `
                                <div class="name-avatar d-flex align-items-center">
                                    <div class="avatar mr-2 flex-shrink-0">
                                        <img src="${row.profile_photo}" 
                                             class="border-radius-100 shadow" 
                                             width="40" 
                                             height="40" 
                                             alt="${row.full_name}"
                                             onerror="this.src='${DEFAULT_IMAGE_URL}'">
                                    </div>
                                    <div class="txt">
                                        <div class="weight-600">${row.full_name}</div>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    { data: 'admno', orderable: true },
                     { data: 'StudentID', orderable: true },
                    { data: 'gender', orderable: true },
                    { data: 'admdate', orderable: true },
                    { data: 'claname', orderable: true },
                    {
                        data: 'status',
                        orderable: true,
                        render: function(data, type, row) {
                            var color = data === 'Active' ? 'green' : 'red';
                            return `<span style="color: ${color}; font-weight: bold;">${data}</span>`;
                        }
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                
                                    <div class="table-actions">
                                        <a class="dropdown-item student-report" 
                                           href="#" 
                                           data-id="${row.admno}">
                                            <i class="fa fa-file-text-o"></i>
                                        </a>
                                        
                                    </div>
                                
                            `;
                        }
                    }
                ],
                order: [[1, 'asc']], // Order by emp_id
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
                    emptyTable: "No students found",
                    zeroRecords: "No matching students found"
                }
            });

            // Edit agent
            

            // Confirm termination
            

            
            
        });
       
       












// Helper function to set checkbox based on YES/NO value

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
function showMessage(message, isError) {
    let messageDiv = $('#messageDiv');
    const backgroundColor = isError ? '#f44336' : '#4CAF50';
    
    if (messageDiv.length === 0) {
        // Create new message div with proper background color
        messageDiv = $(`
            <div id="messageDiv" style="
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 25px;
                border-radius: 5px;
                color: white;
                z-index: 1051;
                display: block;
                font-weight: bold;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                animation: slideIn 0.5s, fadeOut 0.5s 2.5s;
                background-color: ${backgroundColor};
            ">
                ${message}
            </div>
        `);
        $('body').append(messageDiv);
    } else {
        // Update existing message div
        messageDiv.text(message)
                 .show()
                 .css('background-color', backgroundColor);
    }
    
    // Clear any existing timeout
    if (messageDiv.data('timeout')) {
        clearTimeout(messageDiv.data('timeout'));
    }
    
    // Set new timeout and store reference
    const timeoutId = setTimeout(() => {
        messageDiv.fadeOut();
    }, 3000);
    
    messageDiv.data('timeout', timeoutId);
}

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
// Add this event handler for the student report button
$(document).on('click', '.student-report', function(e) {
    e.preventDefault();
    var admno = $(this).data('id');
    loadStudentReport(admno);
});

// Function to load student report
function loadStudentReport(admno) {
    $('#studentReportModal').modal('show');
    $('#reportContent').html(`
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    `);
    
    $.ajax({
        url: studentReportUrl + '/' + admno,
        type: 'GET',
        success: function(response) {
            $('#reportContent').html(response.html);
        },
        error: function(xhr, status, error) {
            $('#reportContent').html(`
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> 
                    Error loading report: ${xhr.responseJSON?.message || 'Unknown error'}
                </div>
            `);
        }
    });
}

// Function to print report
function printReport() {
    var printContents = document.getElementById('reportContent').innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload(); // Reload to restore event handlers
}

$(document).on('click', '#allstudents', function(e) {
    e.preventDefault();
    loadAllStudentsReport();
});

// Apply filters
$(document).on('click', '#applyFilters', function(e) {
    e.preventDefault();
    loadAllStudentsReport();
});

// Change grouping
$(document).on('change', '#groupBySelect', function(e) {
    loadAllStudentsReport();
});

// Function to load all students report
function loadAllStudentsReport() {
    $('#allStudentsReportModal').modal('show');
    $('#allReportContent').html(`
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    `);
    
    var groupBy = $('#groupBySelect').val() || 'class';
    var filterClass = $('#filterClass').val() || '';
    var filterBranch = $('#filterBranch').val() || '';
    
    $.ajax({
        url: allStudentsReportUrl,
        type: 'GET',
        data: {
            group_by: groupBy,
            class_id: filterClass,
            branch_id: filterBranch
        },
        success: function(response) {
            $('#allReportContent').html(response.html);
            
            // Populate filter dropdowns if not already populated
            if (response.classes && $('#filterClass option').length <= 1) {
                response.classes.forEach(function(cls) {
                    $('#filterClass').append(`<option value="${cls.ID}">${cls.claname}</option>`);
                });
            }
            
            if (response.branches && $('#filterBranch option').length <= 1) {
                response.branches.forEach(function(branch) {
                    $('#filterBranch').append(`<option value="${branch.ID}">${branch.branchname}</option>`);
                });
            }
        },
        error: function(xhr, status, error) {
            $('#allReportContent').html(`
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> 
                    Error loading report: ${xhr.responseJSON?.message || 'Unknown error'}
                </div>
            `);
        }
    });
}

// Function to print all report
function printAllReport() {
    var printContents = document.getElementById('allReportContent').innerHTML;
    var windowPrint = window.open('', '', 'width=900,height=650');
    windowPrint.document.write('<html><head><title>All Students Report</title>');
    windowPrint.document.write('<style>');
    windowPrint.document.write(`
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #34495e; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .group-header { background-color: #ecf0f1; font-weight: bold; }
        .report-header { text-align: center; margin-bottom: 20px; }
        .summary-box { margin: 20px 0; padding: 15px; background-color: #f8f9fa; border: 1px solid #ddd; }
    `);
    windowPrint.document.write('</style></head><body>');
    windowPrint.document.write(printContents);
    windowPrint.document.write('</body></html>');
    windowPrint.document.close();
    windowPrint.focus();
    windowPrint.print();
    windowPrint.close();
}

// Function to export to Excel (basic implementation)
function exportToExcel() {
    var groupBy = $('#groupBySelect').val() || 'class';
    var filterClass = $('#filterClass').val() || '';
    var filterBranch = $('#filterBranch').val() || '';
    
    window.location.href = '{{ route("students.export") }}?group_by=' + groupBy + 
                          '&class_id=' + filterClass + '&branch_id=' + filterBranch;
}
             