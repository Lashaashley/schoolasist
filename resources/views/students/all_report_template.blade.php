<style>
    .all-report-container {
        font-family: Arial, sans-serif;
    }
    
    .report-header {
        text-align: center;
        border-bottom: 3px solid #333;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    
    .report-header .logo {
        max-height: 80px;
        width: auto;
        margin-bottom: 10px;
    }
    
    .report-header .school-name {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin: 10px 0;
    }
    
    .report-header .school-motto {
        font-style: italic;
        color: #666;
        margin: 5px 0;
    }
    
    .report-header .school-contact {
        font-size: 12px;
        color: #666;
        margin: 5px 0;
    }
    
    .report-title {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        margin: 20px 0;
        text-transform: uppercase;
        color: #2c3e50;
    }
    
    .summary-box {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 20px;
        margin: 20px 0;
    }
    
    .summary-box h4 {
        margin-bottom: 15px;
        color: #2c3e50;
    }
    
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    
    .summary-item {
        background-color: white;
        padding: 15px;
        border-radius: 5px;
        border-left: 4px solid #3498db;
    }
    
    .summary-item .label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
    }
    
    .summary-item .value {
        font-size: 24px;
        font-weight: bold;
        color: #2c3e50;
    }
    
    .group-section {
        margin: 30px 0;
    }
    
    .group-header {
        background-color: #34495e;
        color: white;
        padding: 12px 15px;
        margin-bottom: 15px;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
    }
    
    .students-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    
    .students-table th {
        background-color: #34495e;
        color: white;
        padding: 12px 10px;
        text-align: left;
        font-weight: bold;
        font-size: 13px;
    }
    
    .students-table td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        font-size: 13px;
    }
    
    .students-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    
    .students-table tr:hover {
        background-color: #ecf0f1;
    }
    
    .student-photo {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #ddd;
    }
    
    .group-summary {
        background-color: #ecf0f1;
        padding: 10px 15px;
        margin-top: 10px;
        border-radius: 5px;
        font-weight: bold;
    }
    
    @media print {
        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .students-table {
            font-size: 10px;
        }
        
        .students-table th,
        .students-table td {
            padding: 6px 4px;
        }
    }
</style>

<div class="all-report-container">
    <!-- Header -->
    <div class="report-header">
        <div class="logo-section">
            @if($school && $school->logo)
                <img src="{{ asset('storage/' . $school->logo) }}" alt="School Logo" class="logo">
            @endif
        </div>
        
        <div class="school-details">
            @if($school)
                <div class="school-name">{{ $school->schname ?? 'School Name' }}</div>
                
                @if($school->motto)
                    <div class="school-motto">"{{ $school->motto }}"</div>
                @endif
                
                <div class="school-contact">
                    @if($school->pobox) P.O. Box {{ $school->pobox }} @endif
                    @if($school->physaddres) | {{ $school->physaddres }} @endif
                    @if($school->email) | Email: {{ $school->email }} @endif
                </div>
            @endif
        </div>
    </div>

    <div class="report-title">All Students Report - Grouped by {{ ucfirst(str_replace('_', ' ', $groupBy)) }}</div>

    <!-- Summary Statistics -->
    <div class="summary-box">
        <h4><i class="fa fa-bar-chart"></i> Summary Statistics</h4>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Total Students</div>
                <div class="value">{{ $totalStudents }}</div>
            </div>
            <div class="summary-item" style="border-left-color: #3498db;">
                <div class="label">Male Students</div>
                <div class="value">{{ $maleCount }}</div>
            </div>
            <div class="summary-item" style="border-left-color: #e74c3c;">
                <div class="label">Female Students</div>
                <div class="value">{{ $femaleCount }}</div>
            </div>
            <div class="summary-item" style="border-left-color: #f39c12;">
                <div class="label">Boarders</div>
                <div class="value">{{ $boardersCount }}</div>
            </div>
            <div class="summary-item" style="border-left-color: #27ae60;">
                <div class="label">Day Scholars</div>
                <div class="value">{{ $dayScholarsCount }}</div>
            </div>
        </div>
    </div>

    <!-- Grouped Students -->
    @foreach($groupedStudents as $groupKey => $studentsInGroup)
        <div class="group-section">
            <div class="group-header">
                <i class="fa fa-users"></i> {{ $groupNames[$groupKey] }} 
                <span style="float: right;">({{ count($studentsInGroup) }} students)</span>
            </div>
            
            <table class="students-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 60px;">Photo</th>
                        <th>Adm No</th>
                        <th>Full Name</th>
                        <th>Gender</th>
                        <th>Class</th>
                        <th>Stream</th>
                        <th>Status</th>
                        @if($groupBy !== 'house')
                        <th>House</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentsInGroup as $index => $student)
                        @php
                            $class = \App\Models\Classes::find($student->claid);
                            $stream = \App\Models\Streams::find($student->stream);
                            $house = $student->houseid ? \App\Models\Houses::find($student->houseid) : null;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ $student->photo ? asset('storage/' . $student->photo) : asset('images/NO-IMAGE-AVAILABLE.jpg') }}" 
                                     alt="{{ $student->sirname }}" 
                                     class="student-photo">
                            </td>
                            <td>{{ $student->admno }}</td>
                            <td>{{ $student->sirname }} {{ $student->othername }}</td>
                            <td>{{ ucfirst($student->gender) }}</td>
                            <td>{{ $class->claname ?? 'N/A' }}</td>
                            <td>{{ $stream->strmname ?? 'N/A' }}</td>
                            <td>{{ ucfirst($student->border) }}</td>
                            @if($groupBy !== 'house')
                            <td>{{ $house ? $house->housen : 'N/A' }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="group-summary">
                Total in {{ $groupNames[$groupKey] }}: {{ count($studentsInGroup) }} students 
                ({{ count(array_filter($studentsInGroup, function($s) { return $s->gender == 'male'; })) }} Male, 
                {{ count(array_filter($studentsInGroup, function($s) { return $s->gender == 'female'; })) }} Female)
            </div>
        </div>
    @endforeach

    <!-- Report Footer -->
    <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ddd; padding-top: 20px;">
        <p>Generated on {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>
        <p>Total Students: {{ $totalStudents }}</p>
    </div>
</div>