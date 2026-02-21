<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fee Report - {{ $reportTitle }}</title>
    <style>
        @page {
            margin: 100px 50px 80px 50px;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
        }
        
        .header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 80px;
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header-content {
            display: table;
            width: 100%;
        }
        
        .logo-section {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
            text-align: left;
        }
        
        .logo {
            max-height: 60px;
            width: auto;
        }
        
        .school-details {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        
        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin: 5px 0;
        }
        
        .school-motto {
            font-style: italic;
            font-size: 11px;
            color: #666;
            margin: 3px 0;
        }
        
        .school-contact {
            font-size: 9px;
            color: #666;
            margin: 3px 0;
        }
        
        .footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        
        .page-number:after {
            content: "Page " counter(page);
        }
        
        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0;
            color: #2c3e50;
            text-transform: uppercase;
        }
        
        .report-info {
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
        }
        
        .report-info p {
            margin: 3px 0;
            font-size: 10px;
        }
        
        .summary-box {
            margin: 20px 0;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-item {
            display: table-cell;
            padding: 15px;
            text-align: center;
            border-right: 1px solid #ddd;
            width: 25%;
        }
        
        .summary-item:last-child {
            border-right: none;
        }
        
        .summary-label {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th {
            background-color: #34495e;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            border: 1px solid #2c3e50;
        }
        
        td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-success {
            color: #27ae60;
            font-weight: bold;
        }
        
        .text-warning {
            color: #f39c12;
            font-weight: bold;
        }
        
        .text-danger {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .badge-success {
            background-color: #27ae60;
            color: white;
        }
        
        .badge-info {
            background-color: #3498db;
            color: white;
        }
        
        .badge-warning {
            background-color: #f39c12;
            color: white;
        }
        
        .badge-danger {
            background-color: #e74c3c;
            color: white;
        }
        
        .table-success {
            background-color: #d4edda !important;
        }
        
        .table-warning {
            background-color: #fff3cd !important;
        }
        
        .table-info {
            background-color: #d1ecf1 !important;
        }
        
        .alert {
            padding: 10px;
            margin: 15px 0;
            border-radius: 4px;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border-left: 4px solid #f39c12;
            color: #856404;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-left: 4px solid #3498db;
            color: #0c5460;
        }
        
        .total-row {
            background-color: #ecf0f1 !important;
            font-weight: bold;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            @if($school)
                <div class="logo-section">
                    @if($school->logo)
                        <img src="{{ public_path('storage/' . $school->logo) }}" alt="School Logo" class="logo">
                    @endif
                </div>
                
                <div class="school-details">
                    <div class="school-name">{{ $school->schname ?? 'School Name' }}</div>
                    
                    @if($school->motto)
                        <div class="school-motto">"{{ $school->motto }}"</div>
                    @endif
                    
                    <div class="school-contact">
                        @if($school->pobox) P.O. Box {{ $school->pobox }} @endif
                        @if($school->physaddres) | {{ $school->physaddres }} @endif
                        @if($school->email) | Email: {{ $school->email }} @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>Generated on {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</div>
        <div class="page-number"></div>
    </div>

    <!-- Content -->
    <div class="report-title">{{ $reportTitle }}</div>

    <!-- Report Information -->
    <div class="report-info">
        <p><strong>Period:</strong> {{ $periodName }}</p>
        @if($filters['branch'])
            <p><strong>Branch:</strong> {{ $filters['branch'] }}</p>
        @endif
        @if($filters['class'])
            <p><strong>Class:</strong> {{ $filters['class'] }}</p>
        @endif
        @if($filters['status'])
            <p><strong>Status:</strong> {{ $filters['status'] }}</p>
        @endif
        @if($filters['from_date'] && $filters['to_date'])
            <p><strong>Date Range:</strong> {{ $filters['from_date'] }} to {{ $filters['to_date'] }}</p>
        @endif
    </div>

    <!-- Summary Box -->
    <div class="summary-box">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Expected</div>
                <div class="summary-value">KSh {{ number_format($summary['total_expected'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Collected</div>
                <div class="summary-value text-success">KSh {{ number_format($summary['total_collected'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Pending</div>
                <div class="summary-value text-warning">KSh {{ number_format($summary['total_pending'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Collection Rate</div>
                <div class="summary-value">{{ $summary['collection_rate'] }}%</div>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    {!! $reportTable !!}

    <!-- Additional Notes -->
    @if($reportType === 'defaulters')
        <div class="alert alert-warning">
            <strong>Note:</strong> This report shows all students with outstanding fee balances. Immediate follow-up is recommended.
        </div>
    @endif

    @if($reportType === 'overpayment')
        <div class="alert alert-info">
            <strong>Note:</strong> Overpayments should be verified and either refunded or applied to future periods.
        </div>
    @endif
</body>
</html>