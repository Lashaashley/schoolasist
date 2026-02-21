<style>
    .report-container {
        font-family: Arial, sans-serif;
        max-width: 100%;
        margin: 0 auto;
    }
    
    .report-header {
        text-align: center;
        border-bottom: 3px solid #333;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    
    .report-header .logo-section {
        margin-bottom: 10px;
    }
    
    .report-header .logo {
        max-height: 80px;
        width: auto;
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
    
    .student-photo {
        text-align: center;
        margin: 20px 0;
    }
    
    .student-photo img {
        max-width: 150px;
        height: 150px;
        object-fit: cover;
        border: 3px solid #ddd;
        border-radius: 5px;
    }
    
    .info-section {
        margin: 20px 0;
    }
    
    .section-title {
        font-size: 16px;
        font-weight: bold;
        background-color: #34495e;
        color: white;
        padding: 8px 15px;
        margin-bottom: 15px;
        border-radius: 3px;
    }
    
    .info-row {
        display: flex;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    
    .info-label {
        flex: 0 0 40%;
        font-weight: bold;
        color: #555;
    }
    
    .info-value {
        flex: 1;
        color: #333;
    }
    
    .fee-table {
        width: 100%;
        border-collapse: collapse;
        margin: 15px 0;
    }
    
    .fee-table th {
        background-color: #34495e;
        color: white;
        padding: 10px;
        text-align: left;
        font-weight: bold;
    }
    
    .fee-table td {
        padding: 8px 10px;
        border-bottom: 1px solid #ddd;
    }
    
    .fee-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    
    .fee-table tfoot td {
        font-weight: bold;
        background-color: #ecf0f1;
        padding: 10px;
        border-top: 2px solid #34495e;
    }
    
    .status-badge {
        padding: 4px 10px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .status-pending {
        background-color: #f39c12;
        color: white;
    }
    
    .status-paid {
        background-color: #27ae60;
        color: white;
    }
    
    .status-partial {
        background-color: #3498db;
        color: white;
    }
    
    @media print {
        .modal-header, .modal-footer {
            display: none;
        }
        
        .report-container {
            width: 100%;
            margin: 0;
            padding: 20px;
        }
    }
</style>

<div class="report-container">
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

    <div class="report-title">Student Information Report</div>

    <!-- Student Photo -->
    <div class="student-photo">
        <img src="{{ $student->photo ? asset('storage/' . $student->photo) : asset('images/NO-IMAGE-AVAILABLE.jpg') }}" 
             alt="{{ $student->sirname }} {{ $student->othername }}">
    </div>

    <!-- Personal Information -->
    <div class="info-section">
        <div class="section-title">Personal Information</div>
        
        <div class="info-row">
            <div class="info-label">Admission Number:</div>
            <div class="info-value">{{ $student->admno }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Full Name:</div>
            <div class="info-value">{{ $student->sirname }} {{ $student->othername }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Gender:</div>
            <div class="info-value">{{ ucfirst($student->gender) }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Date of Birth:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($student->dateob)->format('d M Y') }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Admission Date:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($student->admdate)->format('d M Y') }}</div>
        </div>
    </div>

    <!-- Academic Information -->
    <div class="info-section">
        <div class="section-title">Academic Information</div>
        
        <div class="info-row">
            <div class="info-label">Branch:</div>
            <div class="info-value">{{ $branch->branchname ?? 'N/A' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Class:</div>
            <div class="info-value">{{ $class->claname ?? 'N/A' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Stream:</div>
            <div class="info-value">{{ $stream->strmname ?? 'N/A' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Boarding Status:</div>
            <div class="info-value">{{ ucfirst($student->border) }}</div>
        </div>
        
        @if($student->border === 'yes' && $house)
        <div class="info-row">
            <div class="info-label">House:</div>
            <div class="info-value">{{ $house->housen }}</div>
        </div>
        @endif
    </div>

    <!-- Parent/Guardian Information -->
    @if($parent)
    <div class="info-section">
        <div class="section-title">Parent/Guardian Information</div>
        
        <div class="info-row">
            <div class="info-label">Name:</div>
            <div class="info-value">{{ $parent->surname }} {{ $parent->othername }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Type:</div>
            <div class="info-value">{{ ucfirst($parent->typpe) }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Phone:</div>
            <div class="info-value">{{ $parent->phoneno }}</div>
        </div>
        
        @if($parent->email)
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $parent->email }}</div>
        </div>
        @endif
        
        @if($parent->workplace)
        <div class="info-row">
            <div class="info-label">Workplace:</div>
            <div class="info-value">{{ $parent->workplace }}</div>
        </div>
        @endif
    </div>
    @endif

    <!-- Siblings -->
    @if($siblings->count() > 0)
    <div class="info-section">
        <div class="section-title">Siblings</div>
        
        @foreach($siblings as $sibling)
        <div class="info-row">
            <div class="info-label">{{ $sibling->admno }}:</div>
            <div class="info-value">{{ $sibling->sirname }} {{ $sibling->othername }}</div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Fee Information -->
    <div class="info-section">
        <div class="section-title">Fee Information - {{ $activePeriod->pname ?? 'Current Period' }}</div>
        
        <table class="fee-table">
            <thead>
                <tr>
                    <th>Fee Item</th>
                    <th>Amount</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fees as $fee)
                <tr>
                    <td>{{ $fee->feename }}</td>
                    <td>{{ number_format($fee->amount, 2) }}</td>
                    <td>{{ number_format($fee->paid, 2) }}</td>
                    <td>{{ number_format($fee->balance, 2) }}</td>
                    <td>
                        <span class="status-badge status-{{ strtolower($fee->status) }}">
                            {{ $fee->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">No fee records found</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td>{{ number_format($totalFees, 2) }}</td>
                    <td>{{ number_format($totalPaid, 2) }}</td>
                    <td>{{ number_format($totalBalance, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Report Footer -->
    <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #666;">
        <p>Generated on {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>
    </div>
</div>