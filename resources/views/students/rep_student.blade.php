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
                <div class="form-group">
                    <button class="btn btn-enhanced btn-draft" id="allstudents">
                        <i class="fa fa-table"></i> All Students
                    </button>
                </div>
                
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
                                
                                <th class="datatable-nosort">Report</th>
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
    <!-- Student Report Modal -->
<div class="modal fade" id="studentReportModal" tabindex="-1" role="dialog" aria-labelledby="studentReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentReportModalLabel">Student Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="reportContent">
                <!-- Report content will be loaded here -->
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printReport()">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<!-- All Students Report Modal -->
<div class="modal fade" id="allStudentsReportModal" tabindex="-1" role="dialog" aria-labelledby="allStudentsReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allStudentsReportModalLabel">All Students Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Filter Options -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label>Group By:</label>
                        <select class="form-control" id="groupBySelect">
                            <option value="class">Class</option>
                            <option value="branch">Branch</option>
                            <option value="gender">Gender</option>
                            <option value="border">Boarding Status</option>
                            <option value="house">House</option>
                            <option value="none">No Grouping</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Filter by Class:</label>
                        <select class="form-control" id="filterClass">
                            <option value="">All Classes</option>
                            <!-- Will be populated dynamically -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Filter by Branch:</label>
                        <select class="form-control" id="filterBranch">
                            <option value="">All Branches</option>
                            <!-- Will be populated dynamically -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <button class="btn btn-primary btn-block" id="applyFilters">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                    </div>
                </div>
                
                <div id="allReportContent">
                    <!-- Report content will be loaded here -->
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="exportToExcel()">
                    <i class="fa fa-file-excel-o"></i> Export to Excel
                </button>
                <button type="button" class="btn btn-primary" onclick="printAllReport()">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Edit Staff Modal -->

    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script> 
        const amanage = '{{ route("students.data") }}';
        

        var DEFAULT_IMAGE_URL = "{{ asset('uploads/NO-IMAGE-AVAILABLE.jpg') }}";
        var studentReportUrl = '{{ route("students.report", "") }}';
        var allStudentsReportUrl = '{{ route("students.all-report") }}';
        
  
        
       

    </script>
    <script src="{{ asset('js/sreports.js') }}"></script>
    
    <script> 
 
       
    </script>
    
   
</x-custom-admin-layout>