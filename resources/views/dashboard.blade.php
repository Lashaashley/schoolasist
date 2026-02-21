<x-custom-admin-layout>
    <div class="min-height-200px">
        <div class="page-header">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="title">
                        <h4>Dashboard</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-6 col-sm-12 text-right">
                    <button class="btn btn-primary" id="refreshDashboard">
                        <i class="fa fa-refresh"></i> Refresh Data
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <!-- Total Students Card -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark" id="totalStudents">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="font-14 text-secondary weight-500">Total Students</div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon" style="color: #3498db;">
                                <i class="fa fa-users" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Teachers Card -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark" id="totalTeachers">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="font-14 text-secondary weight-500">Total Teachers</div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon" style="color: #e74c3c;">
                                <i class="fa fa-user-secret" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Collected Card -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark" id="revenueCollected">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="font-14 text-secondary weight-500">Revenue Collected</div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon" style="color: #27ae60;">
                                <i class="fa fa-money" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Pending Card -->
            <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                <div class="card-box height-100-p widget-style3">
                    <div class="d-flex flex-wrap">
                        <div class="widget-data">
                            <div class="weight-700 font-24 text-dark" id="revenuePending">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                            <div class="font-14 text-secondary weight-500">Revenue Pending</div>
                        </div>
                        <div class="widget-icon">
                            <div class="icon" style="color: #f39c12;">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row">
            <!-- Student Distribution by Gender -->
            <div class="col-xl-6 col-lg-6 col-md-6 mb-20">
                <div class="card-box height-100-p pd-20">
                    <h2 class="h4 mb-20">Student Distribution</h2>
                    <div id="studentGenderChart"></div>
                </div>
            </div>

            <!-- Student Distribution by Class -->
            <div class="col-xl-6 col-lg-6 col-md-6 mb-20">
                <div class="card-box height-100-p pd-20">
                    <h2 class="h4 mb-20">Students by Class</h2>
                    <div id="studentClassChart"></div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row">
            <!-- Revenue Analytics -->
            <div class="col-xl-8 col-lg-8 col-md-12 mb-20">
                <div class="card-box height-100-p pd-20">
                    <h2 class="h4 mb-20">Revenue Analytics - Current Period</h2>
                    <div id="revenueChart"></div>
                </div>
            </div>

            <!-- Fee Collection Status -->
            <div class="col-xl-4 col-lg-4 col-md-12 mb-20">
                <div class="card-box height-100-p pd-20">
                    <h2 class="h4 mb-20">Fee Collection Status</h2>
                    <div id="feeStatusChart"></div>
                </div>
            </div>
        </div>

        <!-- Charts Row 3 -->
        <div class="row">
            <!-- Academic Performance by Subject -->
            <div class="col-xl-12 col-lg-12 col-md-12 mb-20">
                <div class="card-box height-100-p pd-20">
                    <div class="d-flex justify-content-between align-items-center mb-20">
                        <h2 class="h4 mb-0">Academic Performance by Subject</h2>
                        <select class="form-control" id="performancePeriodFilter" style="width: 200px;">
                            <option value="">Loading periods...</option>
                        </select>
                    </div>
                    <div id="subjectPerformanceChart"></div>
                </div>
            </div>
        </div>

        <!-- Charts Row 4 -->
        <div class="row">
            <!-- Top Performing Classes -->
            <div class="col-xl-6 col-lg-6 col-md-12 mb-20">
                <div class="card-box height-100-p pd-20">
                    <h2 class="h4 mb-20">Top Performing Classes</h2>
                    <div id="classPerformanceChart"></div>
                </div>
            </div>

            <!-- Boarding vs Day Scholars -->
            <div class="col-xl-6 col-lg-6 col-md-12 mb-20">
                <div class="card-box height-100-p pd-20">
                    <h2 class="h4 mb-20">Boarding vs Day Scholars</h2>
                    <div id="boardingChart"></div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Table -->
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 mb-20">
                <div class="card-box height-100-p pd-20">
                    <h2 class="h4 mb-20">Recent Fee Payments</h2>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="recentPaymentsTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Student</th>
                                    <th>Adm No</th>
                                    <th>Class</th>
                                    <th>Amount Paid</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <i class="fa fa-spinner fa-spin"></i> Loading...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script> 
        var dashboardDataUrl = '{{ route("dashboard.data") }}';

    </script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
   
    
</x-custom-admin-layout>