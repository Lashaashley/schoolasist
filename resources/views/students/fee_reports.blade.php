<x-custom-admin-layout>
    <div class="min-height-200px">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>Fee Financial Reports</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Fee Reports</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card-box mb-30">
            <div class="pd-20">
                <h4 class="text-blue h4">Report Filters</h4>
                <p class="mb-30">Select filters to generate your financial report</p>
                
                <form id="feeReportFilters">
                    <div class="row">
                        <!-- Report Type -->
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Report Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="reportType" name="report_type" required>
                                    <option value="summary">Summary Report</option>
                                    <option value="detailed">Detailed Report</option>
                                    <option value="class">By Class</option>
                                    <option value="branch">By Branch</option>
                                    <option value="student">By Student</option>
                                    <option value="feeitem">By Fee Item</option>
                                    <option value="defaulters">Defaulters Report</option>
                                    <option value="overpayment">Overpayment Report</option>
                                </select>
                            </div>
                        </div>

                        <!-- Period Filter -->
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Period <span class="text-danger">*</span></label>
                                <select class="form-control" id="periodFilter" name="period" required>
                                    <option value="">Loading periods...</option>
                                </select>
                            </div>
                        </div>

                        <!-- Branch Filter -->
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Branch/Campus</label>
                                <select class="form-control" id="branchFilter" name="branch">
                                    <option value="">All Branches</option>
                                </select>
                            </div>
                        </div>

                        <!-- Class Filter -->
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Class</label>
                                <select class="form-control" id="classFilter" name="class">
                                    <option value="">All Classes</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Date Range -->
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" class="form-control" id="fromDate" name="from_date">
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" class="form-control" id="toDate" name="to_date">
                            </div>
                        </div>

                        <!-- Payment Status -->
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Payment Status</label>
                                <select class="form-control" id="statusFilter" name="status">
                                    <option value="">All Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Partial">Partial Payment</option>
                                    <option value="Paid">Fully Paid</option>
                                </select>
                            </div>
                        </div>

                        <!-- Boarding Status -->
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label>Boarding Status</label>
                                <select class="form-control" id="boardingFilter" name="boarding">
                                    <option value="">All Students</option>
                                    <option value="yes">Boarders Only</option>
                                    <option value="no">Day Scholars Only</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-bar-chart"></i> Generate Report
                            </button>
                            <button type="button" class="btn btn-secondary" id="resetFilters">
                                <i class="fa fa-refresh"></i> Reset Filters
                            </button>
                            <button type="button" class="btn btn-danger" id="exportPDF" disabled>
                                <i class="fa fa-file-pdf-o"></i> Export to PDF
                            </button>
                            <button type="button" class="btn btn-success" id="exportExcel" disabled>
                                <i class="fa fa-file-excel-o"></i> Export to Excel
                            </button>
                            <button type="button" class="btn btn-info" id="printReport" disabled>
                                <i class="fa fa-print"></i> Print Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row" id="summaryCards" style="display: none;">
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark" id="totalExpected">KSh 0</div>
                            <div class="font-14 text-secondary weight-500">Total Expected</div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon" style="color: #3498db;">
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark" id="totalCollected">KSh 0</div>
                            <div class="font-14 text-secondary weight-500">Total Collected</div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon" style="color: #27ae60;">
                                <i class="fa fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark" id="totalPending">KSh 0</div>
                            <div class="font-14 text-secondary weight-500">Total Pending</div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon" style="color: #f39c12;">
                                <i class="fa fa-clock-o"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark" id="collectionRate">0%</div>
                            <div class="font-14 text-secondary weight-500">Collection Rate</div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon" style="color: #9b59b6;">
                                <i class="fa fa-percent"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row" id="chartsSection" style="display: none;">
            <div class="col-xl-6 col-lg-6 col-md-12 mb-20">
                <div class="card-box height-100-p pd-20">
                    <h4 class="h4 text-blue mb-20">Collection Overview</h4>
                    <div id="collectionChart"></div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6 col-md-12 mb-20">
                <div class="card-box height-100-p pd-20">
                    <h4 class="h4 text-blue mb-20">Payment Status Distribution</h4>
                    <div id="statusChart"></div>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        <div class="card-box mb-30" id="reportContent" style="display: none;">
            <div class="pd-20">
                <div class="clearfix mb-20">
                    <div class="pull-left">
                        <h4 class="text-blue h4" id="reportTitle">Report</h4>
                    </div>
                </div>
                
                <div id="reportTable"></div>
            </div>
        </div>
    </div>

    
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    
    <script>
        var feeReportDataUrl = '{{ route("fee-reports.data") }}';
        var currentReportData = null;

        $(document).ready(function() {
            loadFilters();

            // Form submission
            $('#feeReportFilters').on('submit', function(e) {
                e.preventDefault();
                generateReport();
            });

            // Reset filters
            $('#resetFilters').on('click', function() {
                $('#feeReportFilters')[0].reset();
                $('#summaryCards').hide();
                $('#chartsSection').hide();
                $('#reportContent').hide();
                $('#exportExcel, #printReport').prop('disabled', true);
            });

            // Export to Excel
            $('#exportExcel').on('click', function() {
                exportReport('excel');
            });

            // Print Report
            $('#printReport').on('click', function() {
                exportReport('print');
            });

            // Dynamic class loading based on branch
            $('#branchFilter').on('change', function() {
                var branchId = $(this).val();
                loadClasses(branchId);
            });
            $('#exportPDF').on('click', function() {
    exportReport('pdf');
});

// Update the exportReport function
function exportReport(type) {
    if (!currentReportData) return;

    var formData = $('#feeReportFilters').serialize();
    var url = '{{ route("fee-reports.export") }}?' + formData + '&export_type=' + type;
    
    if (type === 'print') {
        window.open(url, '_blank');
    } else if (type === 'pdf') {
        // Show loading
        $('#exportPDF').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating PDF...');
        
        window.location.href = url;
        
        // Re-enable button after 3 seconds
        setTimeout(function() {
            $('#exportPDF').prop('disabled', false).html('<i class="fa fa-file-pdf-o"></i> Export to PDF');
        }, 3000);
    } else {
        window.location.href = url;
    }
}

// Enable PDF button when report is generated

        });

        function loadFilters() {
            // Load periods
            $.ajax({
                url: '{{ route("fee-reports.filters") }}',
                type: 'GET',
                success: function(response) {
                    // Populate periods
                    var periodSelect = $('#periodFilter');
                    periodSelect.empty();
                    response.periods.forEach(function(period) {
                        var option = $('<option></option>')
                            .val(period.ID)
                            .text(period.periodname);
                        if (period.pstatus === 'Active') {
                            option.prop('selected', true);
                        }
                        periodSelect.append(option);
                    });

                    // Populate branches
                    var branchSelect = $('#branchFilter');
                    branchSelect.empty().append('<option value="">All Branches</option>');
                    response.branches.forEach(function(branch) {
                        branchSelect.append(`<option value="${branch.ID}">${branch.branchname}</option>`);
                    });

                    // Populate classes
                    var classSelect = $('#classFilter');
                    classSelect.empty().append('<option value="">All Classes</option>');
                    response.classes.forEach(function(cls) {
                        classSelect.append(`<option value="${cls.ID}">${cls.claname}</option>`);
                    });
                },
                error: function(xhr) {
                    console.error('Error loading filters:', xhr);
                }
            });
        }

        function loadClasses(branchId) {
            if (!branchId) {
                // Load all classes
                loadFilters();
                return;
            }

            $.ajax({
                url: '{{ route("fee-reports.classes-by-branch") }}',
                type: 'GET',
                data: { branch_id: branchId },
                success: function(response) {
                    var classSelect = $('#classFilter');
                    classSelect.empty().append('<option value="">All Classes</option>');
                    response.classes.forEach(function(cls) {
                        classSelect.append(`<option value="${cls.ID}">${cls.claname}</option>`);
                    });
                }
            });
        }

        function generateReport() {
            var formData = $('#feeReportFilters').serialize();

            // Show loading
            $('#reportContent').show();
            $('#reportTable').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Generating report...</p></div>');

            $.ajax({
                url: '{{ route("fee-reports.data") }}',
                type: 'GET',
                data: formData,
                success: function(response) {
                    currentReportData = response;
                    displayReport(response);
                    $('#exportExcel, #printReport').prop('disabled', false);
                },
                error: function(xhr) {
                    $('#reportTable').html(`
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i> 
                            Error: ${xhr.responseJSON?.message || 'Failed to generate report'}
                        </div>
                    `);
                }
            });
        }

        function displayReport(data) {
            // Update summary cards
            $('#summaryCards').show();
            $('#totalExpected').text('KSh ' + data.summary.total_expected.toLocaleString());
            $('#totalCollected').text('KSh ' + data.summary.total_collected.toLocaleString());
            $('#totalPending').text('KSh ' + data.summary.total_pending.toLocaleString());
            $('#collectionRate').text(data.summary.collection_rate + '%');

            // Display charts
            $('#chartsSection').show();
            renderCharts(data);

            // Display report table
            $('#reportTitle').text(data.report_title);
            $('#reportTable').html(data.report_html);

            $('#exportExcel, #printReport, #exportPDF').prop('disabled', false);
        }

        function renderCharts(data) {
            // Collection Overview Chart
            Highcharts.chart('collectionChart', {
                chart: { type: 'column' },
                title: { text: null },
                xAxis: {
                    categories: ['Expected', 'Collected', 'Pending']
                },
                yAxis: {
                    min: 0,
                    title: { text: 'Amount (KSh)' }
                },
                tooltip: {
                    pointFormat: '<b>KSh {point.y:,.0f}</b>'
                },
                plotOptions: {
                    column: {
                        dataLabels: {
                            enabled: true,
                            format: 'KSh {point.y:,.0f}'
                        }
                    }
                },
                series: [{
                    name: 'Amount',
                    data: [
                        { y: data.summary.total_expected, color: '#3498db' },
                        { y: data.summary.total_collected, color: '#27ae60' },
                        { y: data.summary.total_pending, color: '#f39c12' }
                    ],
                    showInLegend: false
                }]
            });

            // Status Distribution Chart
            Highcharts.chart('statusChart', {
                chart: { type: 'pie' },
                title: { text: null },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.y}</b> ({point.percentage:.1f}%)'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y}'
                        }
                    }
                },
                series: [{
                    name: 'Students',
                    colorByPoint: true,
                    data: data.status_distribution
                }]
            });
        }

        function exportReport(type) {
            if (!currentReportData) return;

            var formData = $('#feeReportFilters').serialize();
            var url = '{{ route("fee-reports.export") }}?' + formData + '&export_type=' + type;
            
            if (type === 'print') {
                window.open(url, '_blank');
            } else {
                window.location.href = url;
            }
        }
    </script>
   
</x-custom-admin-layout>