 
        var dashboardData = null;

        $(document).ready(function() {
            loadDashboardData();

            // Refresh button
            $('#refreshDashboard').on('click', function() {
                $(this).find('i').addClass('fa-spin');
                loadDashboardData();
            });

            // Performance period filter
            $('#performancePeriodFilter').on('change', function() {
                var periodId = $(this).val();
                if (periodId && dashboardData) {
                    loadSubjectPerformanceChart(periodId);
                }
            });
        });

        function loadDashboardData() {
            $.ajax({
                url: dashboardDataUrl,
                type: 'GET',
                success: function(response) {
                    dashboardData = response;
                    updateDashboardCards(response);
                    renderCharts(response);
                    populatePeriodFilter(response.periods);
                    loadRecentPayments(response.recentPayments);
                    $('#refreshDashboard').find('i').removeClass('fa-spin');
                },
                error: function(xhr) {
                    console.error('Error loading dashboard data:', xhr);
                    $('#refreshDashboard').find('i').removeClass('fa-spin');
                    showAlert('danger', 'Error!', 'Failed to load dashboard data');
                }
            });
        }

        function updateDashboardCards(data) {
            $('#totalStudents').text(data.totalStudents.toLocaleString());
            $('#totalTeachers').text(data.totalTeachers.toLocaleString());
            $('#revenueCollected').text('KSh ' + data.revenueCollected.toLocaleString());
            $('#revenuePending').text('KSh ' + data.revenuePending.toLocaleString());
        }

        function renderCharts(data) {
            // Student Gender Distribution
            Highcharts.chart('studentGenderChart', {
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
                            format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f}%)'
                        }
                    }
                },
                series: [{
                    name: 'Students',
                    colorByPoint: true,
                    data: data.studentsByGender
                }]
            });

            // Students by Class
            Highcharts.chart('studentClassChart', {
                chart: { type: 'column' },
                title: { text: null },
                xAxis: {
                    categories: data.studentsByClass.map(item => item.name),
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: { text: 'Number of Students' }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="padding:0"><b>{point.y} students</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [{
                    name: 'Students',
                    data: data.studentsByClass.map(item => item.y),
                    color: '#3498db'
                }]
            });

            // Revenue Analytics
            Highcharts.chart('revenueChart', {
                chart: { type: 'column' },
                title: { text: null },
                xAxis: {
                    categories: ['Projected', 'Collected', 'Pending']
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
                    name: 'Revenue',
                    data: [
                        { y: data.revenueProjected, color: '#3498db' },
                        { y: data.revenueCollected, color: '#27ae60' },
                        { y: data.revenuePending, color: '#f39c12' }
                    ],
                    showInLegend: false
                }]
            });

            // Fee Collection Status
            Highcharts.chart('feeStatusChart', {
                chart: { type: 'pie' },
                title: { text: null },
                tooltip: {
                    pointFormat: '{series.name}: <b>KSh {point.y:,.0f}</b> ({point.percentage:.1f}%)'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b><br>KSh {point.y:,.0f}'
                        }
                    }
                },
                series: [{
                    name: 'Amount',
                    colorByPoint: true,
                    data: data.feeCollectionStatus
                }]
            });

            // Subject Performance
            if (data.currentPeriod) {
                loadSubjectPerformanceChart(data.currentPeriod.ID);
            }

            // Class Performance
            Highcharts.chart('classPerformanceChart', {
                chart: { type: 'bar' },
                title: { text: null },
                xAxis: {
                    categories: data.classPerformance.map(item => item.name),
                    title: { text: null }
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: { text: 'Average Score (%)' }
                },
                tooltip: {
                    pointFormat: '<b>{point.y:.2f}%</b>'
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:.1f}%'
                        }
                    }
                },
                series: [{
                    name: 'Average Score',
                    data: data.classPerformance.map(item => item.y),
                    color: '#9b59b6',
                    showInLegend: false
                }]
            });

            // Boarding vs Day Scholars
            Highcharts.chart('boardingChart', {
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
                    data: data.boardingDistribution
                }]
            });
        }

        function loadSubjectPerformanceChart(periodId) {
            if (!dashboardData) return;

            var performanceData = dashboardData.subjectPerformance[periodId] || [];

            Highcharts.chart('subjectPerformanceChart', {
                chart: { type: 'column' },
                title: { text: null },
                xAxis: {
                    categories: performanceData.map(item => item.name),
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    max: 100,
                    title: { text: 'Average Score (%)' }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="padding:0"><b>{point.y:.2f}%</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y:.1f}%'
                        }
                    }
                },
                series: [{
                    name: 'Average Score',
                    data: performanceData.map(item => item.y),
                    color: '#e74c3c',
                    showInLegend: false
                }]
            });
        }

        function populatePeriodFilter(periods) {
            var select = $('#performancePeriodFilter');
            select.empty();
            
            if (periods && periods.length > 0) {
                periods.forEach(function(period) {
                    var option = $('<option></option>')
                        .val(period.ID)
                        .text(period.periodname);
                    
                    if (period.pstatus === 'Active') {
                        option.prop('selected', true);
                    }
                    
                    select.append(option);
                });
            } else {
                select.append('<option value="">No periods available</option>');
            }
        }

        function loadRecentPayments(payments) {
            var tbody = $('#recentPaymentsTable tbody');
            tbody.empty();

            if (payments && payments.length > 0) {
                payments.forEach(function(payment) {
                    var row = `
                        <tr>
                            <td>${payment.date}</td>
                            <td>${payment.student_name}</td>
                            <td>${payment.admno}</td>
                            <td>${payment.class_name}</td>
                            <td>KSh ${payment.paid.toLocaleString()}</td>
                            <td>KSh ${payment.balance.toLocaleString()}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            } else {
                tbody.html('<tr><td colspan="6" class="text-center">No recent payments found</td></tr>');
            }
        }

        function showAlert(type, title, message) {
            var alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <strong>${title}</strong> ${message}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            `;
            
            if ($('.alert-container').length) {
                $('.alert-container').html(alertHtml);
            } else {
                $('body').prepend('<div class="alert-container" style="position: fixed; top: 70px; right: 20px; z-index: 9999; min-width: 300px;">' + alertHtml + '</div>');
            }
            
            setTimeout(function() {
                $('.alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 5000);
        }