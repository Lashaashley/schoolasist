<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Managefee;
use App\Models\Periods;
use App\Models\Branches;
use App\Models\Classes;
use App\Models\Feeitem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Structure;

class FeeReportController extends Controller
{
   

    public function getFilters()
    {
        $periods = Periods::orderBy('ID', 'desc')->get();
        $branches = Branches::all();
        $classes = Classes::orderBy('clarank')->get();

        return response()->json([
            'periods' => $periods,
            'branches' => $branches,
            'classes' => $classes
        ]);
    }

    public function getClassesByBranch(Request $request)
    {
        $branchId = $request->input('branch_id');
        $classes = Classes::where('caid', $branchId)->orderBy('clarank')->get();

        return response()->json([
            'classes' => $classes
        ]);
    }

    public function getData(Request $request)
    {
        try {
            $reportType = $request->input('report_type', 'summary');
            $periodId = $request->input('period');
            $branchId = $request->input('branch');
            $classId = $request->input('class');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');
            $status = $request->input('status');
            $boarding = $request->input('boarding');

            // Build base query
            $query = DB::table('managefee')
                ->join('students', 'managefee.admno', '=', 'students.admno')
                ->join('tblclasses', 'students.claid', '=', 'tblclasses.ID')
                ->join('branches', 'students.caid', '=', 'branches.ID')
                ->join('feeitems', 'managefee.feeid', '=', 'feeitems.ID')
                ->where('managefee.period', $periodId);

            // Apply filters
            if ($branchId) {
                $query->where('students.caid', $branchId);
            }

            if ($classId) {
                $query->where('students.claid', $classId);
            }

            if ($status) {
                $query->where('managefee.status', $status);
            }

            if ($boarding) {
                $query->where('students.border', $boarding);
            }

            if ($fromDate && $toDate) {
                $query->whereBetween('managefee.updated_at', [$fromDate, $toDate]);
            }

            // Calculate summary
            $summary = $this->calculateSummary($query);

            // Get status distribution
            $statusDistribution = $this->getStatusDistribution($periodId, $branchId, $classId, $boarding);

            // Generate report based on type
            switch ($reportType) {
                case 'detailed':
                    $reportData = $this->getDetailedReport($query);
                    break;
                case 'class':
                    $reportData = $this->getClassReport($periodId, $branchId, $boarding);
                    break;
                case 'branch':
                    $reportData = $this->getBranchReport($periodId);
                    break;
                case 'student':
                    $reportData = $this->getStudentReport($query);
                    break;
                case 'feeitem':
                    $reportData = $this->getFeeItemReport($periodId, $branchId, $classId);
                    break;
                case 'defaulters':
                    $reportData = $this->getDefaultersReport($periodId, $branchId, $classId);
                    break;
                case 'overpayment':
                    $reportData = $this->getOverpaymentReport($periodId, $branchId, $classId);
                    break;
                default:
                    $reportData = $this->getSummaryReport($query);
            }

            return response()->json([
                'summary' => $summary,
                'status_distribution' => $statusDistribution,
                'report_title' => $reportData['title'],
                'report_html' => $reportData['html']
            ]);

        } catch (\Exception $e) {
            Log::error('Fee report error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateSummary($query)
    {
        $clonedQuery = clone $query;
        
        $totals = $clonedQuery->select(
            DB::raw('SUM(managefee.amount) as total_expected'),
            DB::raw('SUM(managefee.paid) as total_collected'),
            DB::raw('SUM(managefee.balance) as total_pending')
        )->first();

        $collectionRate = $totals->total_expected > 0 
            ? round(($totals->total_collected / $totals->total_expected) * 100, 2) 
            : 0;

        return [
            'total_expected' => floatval($totals->total_expected),
            'total_collected' => floatval($totals->total_collected),
            'total_pending' => floatval($totals->total_pending),
            'collection_rate' => $collectionRate
        ];
    }

    private function getStatusDistribution($periodId, $branchId, $classId, $boarding)
    {
        $query = DB::table('managefee')
            ->join('students', 'managefee.admno', '=', 'students.admno')
            ->where('managefee.period', $periodId);

        if ($branchId) {
            $query->where('students.caid', $branchId);
        }

        if ($classId) {
            $query->where('students.claid', $classId);
        }

        if ($boarding) {
            $query->where('students.border', $boarding);
        }

        $distribution = $query->select('managefee.status', DB::raw('COUNT(DISTINCT students.admno) as count'))
            ->groupBy('managefee.status')
            ->get();

        $colors = [
            'Pending' => '#f39c12',
            'Partial' => '#3498db',
            'Paid' => '#27ae60'
        ];

        return $distribution->map(function($item) use ($colors) {
            return [
                'name' => $item->status,
                'y' => intval($item->count),
                'color' => $colors[$item->status] ?? '#95a5a6'
            ];
        })->toArray();
    }

    private function getSummaryReport($query)
    {
        $data = $query->select(
            'tblclasses.claname',
            'branches.branchname',
            DB::raw('COUNT(DISTINCT students.admno) as student_count'),
            DB::raw('SUM(managefee.amount) as total_expected'),
            DB::raw('SUM(managefee.paid) as total_collected'),
            DB::raw('SUM(managefee.balance) as total_pending')
        )
        ->groupBy('tblclasses.ID', 'tblclasses.claname', 'branches.ID', 'branches.branchname')
        ->get();

        $html = '<div class="table-responsive"><table class="table table-striped table-bordered">';
        $html .= '<thead class="bg-primary text-white">';
        $html .= '<tr>';
        $html .= '<th>Branch</th>';
        $html .= '<th>Class</th>';
        $html .= '<th>Students</th>';
        $html .= '<th>Expected (KSh)</th>';
        $html .= '<th>Collected (KSh)</th>';
        $html .= '<th>Pending (KSh)</th>';
        $html .= '<th>Rate (%)</th>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        foreach ($data as $row) {
            $rate = $row->total_expected > 0 ? round(($row->total_collected / $row->total_expected) * 100, 2) : 0;
            $html .= '<tr>';
            $html .= '<td>' . $row->branchname . '</td>';
            $html .= '<td>' . $row->claname . '</td>';
            $html .= '<td>' . number_format($row->student_count) . '</td>';
            $html .= '<td>' . number_format($row->total_expected, 2) . '</td>';
            $html .= '<td class="text-success">' . number_format($row->total_collected, 2) . '</td>';
            $html .= '<td class="text-warning">' . number_format($row->total_pending, 2) . '</td>';
            $html .= '<td>' . $rate . '%</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return [
            'title' => 'Fee Collection Summary Report',
            'html' => $html
        ];
    }

    private function getDetailedReport($query)
    {
        $data = $query->select(
            'students.admno',
            DB::raw("CONCAT(students.sirname, ' ', students.othername) as student_name"),
            'tblclasses.claname',
            'branches.branchname',
            'feeitems.feename',
            'managefee.amount',
            'managefee.paid',
            'managefee.balance',
            'managefee.status'
        )
        ->orderBy('branches.branchname')
        ->orderBy('tblclasses.clarank')
        ->orderBy('students.sirname')
        ->get();

        $html = '<div class="table-responsive"><table class="table table-striped table-bordered table-sm">';
        $html .= '<thead class="bg-primary text-white">';
        $html .= '<tr>';
        $html .= '<th>Adm No</th>';
        $html .= '<th>Student Name</th>';
        $html .= '<th>Branch</th>';
        $html .= '<th>Class</th>';
        $html .= '<th>Fee Item</th>';
        $html .= '<th>Amount (KSh)</th>';
        $html .= '<th>Paid (KSh)</th>';
        $html .= '<th>Balance (KSh)</th>';
        $html .= '<th>Status</th>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        foreach ($data as $row) {
            $statusColor = $row->status == 'Paid' ? 'success' : ($row->status == 'Partial' ? 'info' : 'warning');
            $html .= '<tr>';
            $html .= '<td>' . $row->admno . '</td>';
            $html .= '<td>' . $row->student_name . '</td>';
            $html .= '<td>' . $row->branchname . '</td>';
            $html .= '<td>' . $row->claname . '</td>';
            $html .= '<td>' . $row->feename . '</td>';
            $html .= '<td>' . number_format($row->amount, 2) . '</td>';
            $html .= '<td class="text-success">' . number_format($row->paid, 2) . '</td>';
            $html .= '<td class="text-warning">' . number_format($row->balance, 2) . '</td>';
            $html .= '<td><span class="badge badge-' . $statusColor . '">' . $row->status . '</span></td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return [
            'title' => 'Detailed Fee Report',
            'html' => $html
        ];
    }

    private function getClassReport($periodId, $branchId, $boarding)
    {
        $query = DB::table('managefee')
            ->join('students', 'managefee.admno', '=', 'students.admno')
            ->join('tblclasses', 'students.claid', '=', 'tblclasses.ID')
            ->where('managefee.period', $periodId);

        if ($branchId) {
            $query->where('students.caid', $branchId);
        }

        if ($boarding) {
            $query->where('students.border', $boarding);
        }

        $data = $query->select(
            'tblclasses.claname',
            DB::raw('COUNT(DISTINCT students.admno) as student_count'),
            DB::raw('SUM(managefee.amount) as total_expected'),
            DB::raw('SUM(managefee.paid) as total_collected'),
            DB::raw('SUM(managefee.balance) as total_pending'),
            DB::raw('COUNT(CASE WHEN managefee.status = "Paid" THEN 1 END) as paid_count'),
            DB::raw('COUNT(CASE WHEN managefee.status = "Partial" THEN 1 END) as partial_count'),
            DB::raw('COUNT(CASE WHEN managefee.status = "Pending" THEN 1 END) as pending_count')
        )
        ->groupBy('tblclasses.ID', 'tblclasses.claname')
        ->orderBy('tblclasses.clarank')
        ->get();

        $html = '<div class="table-responsive"><table class="table table-striped table-bordered">';
        $html .= '<thead class="bg-primary text-white">';
        $html .= '<tr>';
        $html .= '<th>Class</th>';
        $html .= '<th>Students</th>';
        $html .= '<th>Expected (KSh)</th>';
        $html .= '<th>Collected (KSh)</th>';
        $html .= '<th>Pending (KSh)</th>';
        $html .= '<th>Fully Paid</th>';
        $html .= '<th>Partial</th>';
        $html .= '<th>Unpaid</th>';
        $html .= '<th>Rate (%)</th>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        foreach ($data as $row) {
            $rate = $row->total_expected > 0 ? round(($row->total_collected / $row->total_expected) * 100, 2) : 0;
            $html .= '<tr>';
            $html .= '<td><strong>' . $row->claname . '</strong></td>';
            $html .= '<td>' . number_format($row->student_count) . '</td>';
            $html .= '<td>' . number_format($row->total_expected, 2) . '</td>';
            $html .= '<td class="text-success"><strong>' . number_format($row->total_collected, 2) . '</strong></td>';
            $html .= '<td class="text-warning">' . number_format($row->total_pending, 2) . '</td>';
            $html .= '<td><span class="badge badge-success">' . $row->paid_count . '</span></td>';
            $html .= '<td><span class="badge badge-info">' . $row->partial_count . '</span></td>';
            $html .= '<td><span class="badge badge-warning">' . $row->pending_count . '</span></td>';
            $html .= '<td><strong>' . $rate . '%</strong></td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return [
            'title' => 'Fee Collection Report by Class',
            'html' => $html
        ];
    }

    private function getBranchReport($periodId)
    {
        $data = DB::table('managefee')
            ->join('students', 'managefee.admno', '=', 'students.admno')
            ->join('branches', 'students.caid', '=', 'branches.ID')
            ->where('managefee.period', $periodId)
            ->select(
                'branches.branchname',
                DB::raw('COUNT(DISTINCT students.admno) as student_count'),
                DB::raw('SUM(managefee.amount) as total_expected'),
                DB::raw('SUM(managefee.paid) as total_collected'),
                DB::raw('SUM(managefee.balance) as total_pending')
            )
            ->groupBy('branches.ID', 'branches.branchname')
            ->get();

        $html = '<div class="table-responsive"><table class="table table-striped table-bordered">';
        $html .= '<thead class="bg-primary text-white">';
        $html .= '<tr>';
        $html .= '<th>Branch/Campus</th>';
        $html .= '<th>Students</th>';
        $html .= '<th>Expected (KSh)</th>';
        $html .= '<th>Collected (KSh)</th>';
        $html .= '<th>Pending (KSh)</th>';
        $html .= '<th>Collection Rate (%)</th>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        foreach ($data as $row) {
            $rate = $row->total_expected > 0 ? round(($row->total_collected / $row->total_expected) * 100, 2) : 0;
            $html .= '<tr>';
            $html .= '<td><strong>' . $row->branchname . '</strong></td>';
            $html .= '<td>' . number_format($row->student_count) . '</td>';
            $html .= '<td>' . number_format($row->total_expected, 2) . '</td>';
            $html .= '<td class="text-success"><strong>' . number_format($row->total_collected, 2) . '</strong></td>';
            $html .= '<td class="text-warning">' . number_format($row->total_pending, 2) . '</td>';
            $html .= '<td><strong>' . $rate . '%</strong></td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return [
            'title' => 'Fee Collection Report by Branch/Campus',
            'html' => $html
        ];
    }

    private function getStudentReport($query)
    {
        $data = $query->select(
            'students.admno',
            DB::raw("CONCAT(students.sirname, ' ', students.othername) as student_name"),
            'tblclasses.claname',
            'branches.branchname',
            DB::raw('SUM(managefee.amount) as total_expected'),
            DB::raw('SUM(managefee.paid) as total_collected'),
            DB::raw('SUM(managefee.balance) as total_pending')
        )
        ->groupBy('students.admno', 'student_name', 'tblclasses.claname', 'branches.branchname')
        ->orderBy('students.sirname')
        ->get();

        $html = '<div class="table-responsive"><table class="table table-striped table-bordered">';
        $html .= '<thead class="bg-primary text-white">';
        $html .= '<tr>';
        $html .= '<th>Adm No</th>';
        $html .= '<th>Student Name</th>';
        $html .= '<th>Branch</th>';
        $html .= '<th>Class</th>';
        $html .= '<th>Expected (KSh)</th>';
        $html .= '<th>Paid (KSh)</th>';
        $html .= '<th>Balance (KSh)</th>';
        $html .= '<th>Rate (%)</th>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        foreach ($data as $row) {
            $rate = $row->total_expected > 0 ? round(($row->total_collected / $row->total_expected) * 100, 2) : 0;
            $rowClass = $row->total_pending <= 0 ? 'table-success' : ($rate > 50 ? 'table-info' : 'table-warning');
            
            $html .= '<tr class="' . $rowClass . '">';
            $html .= '<td>' . $row->admno . '</td>';
            $html .= '<td>' . $row->student_name . '</td>';
            $html .= '<td>' . $row->branchname . '</td>';
            $html .= '<td>' . $row->claname . '</td>';
            $html .= '<td>' . number_format($row->total_expected, 2) . '</td>';
            $html .= '<td class="text-success">' . number_format($row->total_collected, 2) . '</td>';
            $html .= '<td class="text-danger"><strong>' . number_format($row->total_pending, 2) . '</strong></td>';
            $html .= '<td>' . $rate . '%</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return [
            'title' => 'Fee Collection Report by Student',
            'html' => $html
        ];
    }

    private function getFeeItemReport($periodId, $branchId, $classId)
    {
        $query = DB::table('managefee')
            ->join('students', 'managefee.admno', '=', 'students.admno')
            ->join('feeitems', 'managefee.feeid', '=', 'feeitems.ID')
            ->where('managefee.period', $periodId);

        if ($branchId) {
            $query->where('students.caid', $branchId);
        }

        if ($classId) {
            $query->where('students.claid', $classId);
        }

        $data = $query->select(
            'feeitems.feename',
            DB::raw('COUNT(DISTINCT students.admno) as student_count'),
            DB::raw('SUM(managefee.amount) as total_expected'),
            DB::raw('SUM(managefee.paid) as total_collected'),
            DB::raw('SUM(managefee.balance) as total_pending')
        )
        ->groupBy('feeitems.ID', 'feeitems.feename')
        ->get();

        $html = '<div class="table-responsive"><table class="table table-striped table-bordered">';
        $html .= '<thead class="bg-primary text-white">';
        $html .= '<tr>';
        $html .= '<th>Fee Item</th>';
        $html .= '<th>Students</th>';
        $html .= '<th>Expected (KSh)</th>';
        $html .= '<th>Collected (KSh)</th>';
        $html .= '<th>Pending (KSh)</th>';
        $html .= '<th>Collection Rate (%)</th>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        foreach ($data as $row) {
            $rate = $row->total_expected > 0 ? round(($row->total_collected / $row->total_expected) * 100, 2) : 0;
            $html .= '<tr>';
            $html .= '<td><strong>' . $row->feename . '</strong></td>';
            $html .= '<td>' . number_format($row->student_count) . '</td>';
            $html .= '<td>' . number_format($row->total_expected, 2) . '</td>';
            $html .= '<td class="text-success"><strong>' . number_format($row->total_collected, 2) . '</strong></td>';
            $html .= '<td class="text-warning">' . number_format($row->total_pending, 2) . '</td>';
            $html .= '<td>' . $rate . '%</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return [
            'title' => 'Fee Collection Report by Fee Item',
            'html' => $html
        ];
    }

    private function getDefaultersReport($periodId, $branchId, $classId)
    {
        $query = DB::table('managefee')
            ->join('students', 'managefee.admno', '=', 'students.admno')
            ->join('tblclasses', 'students.claid', '=', 'tblclasses.ID')
            ->join('branches', 'students.caid', '=', 'branches.ID')
            ->where('managefee.period', $periodId)
            ->where('managefee.balance', '>', 0);

        if ($branchId) {
            $query->where('students.caid', $branchId);
        }

        if ($classId) {
            $query->where('students.claid', $classId);
        }

        $data = $query->select(
            'students.admno',
            DB::raw("CONCAT(students.sirname, ' ', students.othername) as student_name"),
            'tblclasses.claname',
            'branches.branchname',
            DB::raw('SUM(managefee.amount) as total_expected'),
            DB::raw('SUM(managefee.paid) as total_collected'),
            DB::raw('SUM(managefee.balance) as total_pending')
        )
        ->groupBy('students.admno', 'student_name', 'tblclasses.claname', 'branches.branchname')
        ->having('total_pending', '>', 0)
        ->orderBy('total_pending', 'desc')
        ->get();

        $html = '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Students with outstanding balances</div>';
        $html .= '<div class="table-responsive"><table class="table table-striped table-bordered">';
        $html .= '<thead class="bg-danger text-white">';
        $html .= '<tr>';
        $html .= '<th>Adm No</th>';
        $html .= '<th>Student Name</th>';
        $html .= '<th>Branch</th>';
        $html .= '<th>Class</th>';
        $html .= '<th>Expected (KSh)</th>';
        $html .= '<th>Paid (KSh)</th>';
        $html .= '<th>Outstanding (KSh)</th>';
        $html .= '<th>% Paid</th>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        foreach ($data as $row) {
            $percentPaid = $row->total_expected > 0 ? round(($row->total_collected / $row->total_expected) * 100, 2) : 0;
            $html .= '<tr>';
            $html .= '<td>' . $row->admno . '</td>';
            $html .= '<td><strong>' . $row->student_name . '</strong></td>';
            $html .= '<td>' . $row->branchname . '</td>';
            $html .= '<td>' . $row->claname . '</td>';
            $html .= '<td>' . number_format($row->total_expected, 2) . '</td>';
            $html .= '<td class="text-success">' . number_format($row->total_collected, 2) . '</td>';
            $html .= '<td class="text-danger"><strong>' . number_format($row->total_pending, 2) . '</strong></td>';
            $html .= '<td>' . $percentPaid . '%</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return [
            'title' => 'Fee Defaulters Report',
            'html' => $html
        ];
    }

    private function getOverpaymentReport($periodId, $branchId, $classId)
    {
        $query = DB::table('managefee')
            ->join('students', 'managefee.admno', '=', 'students.admno')
            ->join('tblclasses', 'students.claid', '=', 'tblclasses.ID')
            ->join('branches', 'students.caid', '=', 'branches.ID')
            ->where('managefee.period', $periodId)
            ->whereRaw('managefee.paid > managefee.amount');

        if ($branchId) {
            $query->where('students.caid', $branchId);
        }

        if ($classId) {
            $query->where('students.claid', $classId);
        }

        $data = $query->select(
            'students.admno',
            DB::raw("CONCAT(students.sirname, ' ', students.othername) as student_name"),
            'tblclasses.claname',
            'branches.branchname',
            'managefee.amount',
            'managefee.paid',
            DB::raw('(managefee.paid - managefee.amount) as overpayment')
        )
        ->orderBy('overpayment', 'desc')
        ->get();

        $html = '<div class="alert alert-info"><i class="fa fa-info-circle"></i> Students with overpayments</div>';
        $html .= '<div class="table-responsive"><table class="table table-striped table-bordered">';
        $html .= '<thead class="bg-info text-white">';
        $html .= '<tr>';
        $html .= '<th>Adm No</th>';
        $html .= '<th>Student Name</th>';
        $html .= '<th>Branch</th>';
        $html .= '<th>Class</th>';
        $html .= '<th>Expected (KSh)</th>';
        $html .= '<th>Paid (KSh)</th>';
        $html .= '<th>Overpayment (KSh)</th>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        if ($data->isEmpty()) {
            $html .= '<tr><td colspan="7" class="text-center">No overpayments found</td></tr>';
        } else {
            foreach ($data as $row) {
                $html .= '<tr>';
                $html .= '<td>' . $row->admno . '</td>';
                $html .= '<td><strong>' . $row->student_name . '</strong></td>';
                $html .= '<td>' . $row->branchname . '</td>';
                $html .= '<td>' . $row->claname . '</td>';
                $html .= '<td>' . number_format($row->amount, 2) . '</td>';
                $html .= '<td>' . number_format($row->paid, 2) . '</td>';
                $html .= '<td class="text-primary"><strong>' . number_format($row->overpayment, 2) . '</strong></td>';
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table></div>';

        return [
            'title' => 'Overpayment Report',
            'html' => $html
        ];
    }

    public function export(Request $request)
{
    $exportType = $request->input('export_type', 'excel');
    
    try {
        if ($exportType === 'pdf') {
            return $this->exportToPDF($request);
        } elseif ($exportType === 'excel') {
            return $this->exportToExcel($request);
        } else {
            return $this->exportToPrint($request);
        }
    } catch (\Exception $e) {
        Log::error('Export error: ' . $e->getMessage());
        return back()->with('error', 'Failed to export report: ' . $e->getMessage());
    }
}
private function exportToPDF(Request $request)
{
    // Get all the report data
    $reportType = $request->input('report_type', 'summary');
    $periodId = $request->input('period');
    $branchId = $request->input('branch');
    $classId = $request->input('class');
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');
    $status = $request->input('status');
    $boarding = $request->input('boarding');

    // Get school info
    $school = Structure::first();

    // Get period name
    $period = Periods::find($periodId);
    $periodName = $period ? $period->periodname : 'Unknown Period';

    // Build query
    $query = DB::table('managefee')
        ->join('students', 'managefee.admno', '=', 'students.admno')
        ->join('tblclasses', 'students.claid', '=', 'tblclasses.ID')
        ->join('branches', 'students.caid', '=', 'branches.ID')
        ->join('feeitems', 'managefee.feeid', '=', 'feeitems.ID')
        ->where('managefee.period', $periodId);

    // Apply filters
    if ($branchId) {
        $query->where('students.caid', $branchId);
    }

    if ($classId) {
        $query->where('students.claid', $classId);
    }

    if ($status) {
        $query->where('managefee.status', $status);
    }

    if ($boarding) {
        $query->where('students.border', $boarding);
    }

    if ($fromDate && $toDate) {
        $query->whereBetween('managefee.updated_at', [$fromDate, $toDate]);
    }

    // Calculate summary
    $summary = $this->calculateSummary($query);

    // Generate report based on type
    switch ($reportType) {
        case 'detailed':
            $reportData = $this->getDetailedReportForPDF($query);
            break;
        case 'class':
            $reportData = $this->getClassReportForPDF($periodId, $branchId, $boarding);
            break;
        case 'branch':
            $reportData = $this->getBranchReportForPDF($periodId);
            break;
        case 'student':
            $reportData = $this->getStudentReportForPDF($query);
            break;
        case 'feeitem':
            $reportData = $this->getFeeItemReportForPDF($periodId, $branchId, $classId);
            break;
        case 'defaulters':
            $reportData = $this->getDefaultersReportForPDF($periodId, $branchId, $classId);
            break;
        case 'overpayment':
            $reportData = $this->getOverpaymentReportForPDF($periodId, $branchId, $classId);
            break;
        default:
            $reportData = $this->getSummaryReportForPDF($query);
    }

    // Prepare filter info
    $filters = [
        'branch' => $branchId ? Branches::find($branchId)->branchname : null,
        'class' => $classId ? Classes::find($classId)->claname : null,
        'status' => $status,
        'from_date' => $fromDate,
        'to_date' => $toDate
    ];

    // Generate PDF
    $pdf = PDF::loadView('fee_reports_pdf', [
        'school' => $school,
        'reportTitle' => $reportData['title'],
        'reportType' => $reportType,
        'periodName' => $periodName,
        'summary' => $summary,
        'reportTable' => $reportData['html'],
        'filters' => $filters
    ]);

    // Set paper size and orientation
    $pdf->setPaper('A4', 'landscape');

    // Download the PDF
    $filename = 'fee_report_' . $reportType . '_' . date('Y-m-d_His') . '.pdf';
    return $pdf->download($filename);
}

// PDF-specific report methods (similar to HTML but optimized for PDF)
private function getSummaryReportForPDF($query)
{
    $data = $query->select(
        'tblclasses.claname',
        'branches.branchname',
        DB::raw('COUNT(DISTINCT students.admno) as student_count'),
        DB::raw('SUM(managefee.amount) as total_expected'),
        DB::raw('SUM(managefee.paid) as total_collected'),
        DB::raw('SUM(managefee.balance) as total_pending')
    )
    ->groupBy('tblclasses.ID', 'tblclasses.claname', 'branches.ID', 'branches.branchname')
    ->get();

    $html = '<table>';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Branch</th>';
    $html .= '<th>Class</th>';
    $html .= '<th class="text-center">Students</th>';
    $html .= '<th class="text-right">Expected (KSh)</th>';
    $html .= '<th class="text-right">Collected (KSh)</th>';
    $html .= '<th class="text-right">Pending (KSh)</th>';
    $html .= '<th class="text-center">Rate (%)</th>';
    $html .= '</tr>';
    $html .= '</thead><tbody>';

    $totalStudents = 0;
    $totalExpected = 0;
    $totalCollected = 0;
    $totalPending = 0;

    foreach ($data as $row) {
        $rate = $row->total_expected > 0 ? round(($row->total_collected / $row->total_expected) * 100, 2) : 0;
        
        $totalStudents += $row->student_count;
        $totalExpected += $row->total_expected;
        $totalCollected += $row->total_collected;
        $totalPending += $row->total_pending;

        $html .= '<tr>';
        $html .= '<td>' . $row->branchname . '</td>';
        $html .= '<td>' . $row->claname . '</td>';
        $html .= '<td class="text-center">' . number_format($row->student_count) . '</td>';
        $html .= '<td class="text-right">' . number_format($row->total_expected, 2) . '</td>';
        $html .= '<td class="text-right text-success">' . number_format($row->total_collected, 2) . '</td>';
        $html .= '<td class="text-right text-warning">' . number_format($row->total_pending, 2) . '</td>';
        $html .= '<td class="text-center">' . $rate . '%</td>';
        $html .= '</tr>';
    }

    // Add totals row
    $overallRate = $totalExpected > 0 ? round(($totalCollected / $totalExpected) * 100, 2) : 0;
    $html .= '<tr class="total-row">';
    $html .= '<td colspan="2">TOTAL</td>';
    $html .= '<td class="text-center">' . number_format($totalStudents) . '</td>';
    $html .= '<td class="text-right">' . number_format($totalExpected, 2) . '</td>';
    $html .= '<td class="text-right">' . number_format($totalCollected, 2) . '</td>';
    $html .= '<td class="text-right">' . number_format($totalPending, 2) . '</td>';
    $html .= '<td class="text-center">' . $overallRate . '%</td>';
    $html .= '</tr>';

    $html .= '</tbody></table>';

    return [
        'title' => 'Fee Collection Summary Report',
        'html' => $html
    ];
}

private function getDetailedReportForPDF($query)
{
    $data = $query->select(
        'students.admno',
        DB::raw("CONCAT(students.sirname, ' ', students.othername) as student_name"),
        'tblclasses.claname',
        'branches.branchname',
        'feeitems.feename',
        'managefee.amount',
        'managefee.paid',
        'managefee.balance',
        'managefee.status'
    )
    ->orderBy('branches.branchname')
    ->orderBy('tblclasses.clarank')
    ->orderBy('students.sirname')
    ->get();

    $html = '<table>';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Adm No</th>';
    $html .= '<th>Student Name</th>';
    $html .= '<th>Branch</th>';
    $html .= '<th>Class</th>';
    $html .= '<th>Fee Item</th>';
    $html .= '<th class="text-right">Amount</th>';
    $html .= '<th class="text-right">Paid</th>';
    $html .= '<th class="text-right">Balance</th>';
    $html .= '<th class="text-center">Status</th>';
    $html .= '</tr>';
    $html .= '</thead><tbody>';

    foreach ($data as $row) {
        $statusClass = $row->status == 'Paid' ? 'success' : ($row->status == 'Partial' ? 'info' : 'warning');
        $html .= '<tr>';
        $html .= '<td>' . $row->admno . '</td>';
        $html .= '<td>' . $row->student_name . '</td>';
        $html .= '<td>' . $row->branchname . '</td>';
        $html .= '<td>' . $row->claname . '</td>';
        $html .= '<td>' . $row->feename . '</td>';
        $html .= '<td class="text-right">' . number_format($row->amount, 2) . '</td>';
        $html .= '<td class="text-right text-success">' . number_format($row->paid, 2) . '</td>';
        $html .= '<td class="text-right text-warning">' . number_format($row->balance, 2) . '</td>';
        $html .= '<td class="text-center"><span class="badge badge-' . $statusClass . '">' . $row->status . '</span></td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    return [
        'title' => 'Detailed Fee Report',
        'html' => $html
    ];
}

private function getClassReportForPDF($periodId, $branchId, $boarding)
{
    $query = DB::table('managefee')
        ->join('students', 'managefee.admno', '=', 'students.admno')
        ->join('tblclasses', 'students.claid', '=', 'tblclasses.ID')
        ->where('managefee.period', $periodId);

    if ($branchId) {
        $query->where('students.caid', $branchId);
    }

    if ($boarding) {
        $query->where('students.border', $boarding);
    }

    $data = $query->select(
        'tblclasses.claname',
        DB::raw('COUNT(DISTINCT students.admno) as student_count'),
        DB::raw('SUM(managefee.amount) as total_expected'),
        DB::raw('SUM(managefee.paid) as total_collected'),
        DB::raw('SUM(managefee.balance) as total_pending'),
        DB::raw('COUNT(CASE WHEN managefee.status = "Paid" THEN 1 END) as paid_count'),
        DB::raw('COUNT(CASE WHEN managefee.status = "Partial" THEN 1 END) as partial_count'),
        DB::raw('COUNT(CASE WHEN managefee.status = "Pending" THEN 1 END) as pending_count')
    )
    ->groupBy('tblclasses.ID', 'tblclasses.claname')
    ->orderBy('tblclasses.clarank')
    ->get();

    $html = '<table>';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Class</th>';
    $html .= '<th class="text-center">Students</th>';
    $html .= '<th class="text-right">Expected</th>';
    $html .= '<th class="text-right">Collected</th>';
    $html .= '<th class="text-right">Pending</th>';
    $html .= '<th class="text-center">Paid</th>';
    $html .= '<th class="text-center">Partial</th>';
    $html .= '<th class="text-center">Unpaid</th>';
    $html .= '<th class="text-center">Rate %</th>';
    $html .= '</tr>';
    $html .= '</thead><tbody>';

    foreach ($data as $row) {
        $rate = $row->total_expected > 0 ? round(($row->total_collected / $row->total_expected) * 100, 2) : 0;
        $html .= '<tr>';
        $html .= '<td><strong>' . $row->claname . '</strong></td>';
        $html .= '<td class="text-center">' . number_format($row->student_count) . '</td>';
        $html .= '<td class="text-right">' . number_format($row->total_expected, 2) . '</td>';
        $html .= '<td class="text-right text-success">' . number_format($row->total_collected, 2) . '</td>';
        $html .= '<td class="text-right text-warning">' . number_format($row->total_pending, 2) . '</td>';
        $html .= '<td class="text-center"><span class="badge badge-success">' . $row->paid_count . '</span></td>';
        $html .= '<td class="text-center"><span class="badge badge-info">' . $row->partial_count . '</span></td>';
        $html .= '<td class="text-center"><span class="badge badge-warning">' . $row->pending_count . '</span></td>';
        $html .= '<td class="text-center">' . $rate . '%</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    return [
        'title' => 'Fee Collection Report by Class',
        'html' => $html
    ];
}

private function getBranchReportForPDF($periodId)
{
    $data = DB::table('managefee')
        ->join('students', 'managefee.admno', '=', 'students.admno')
        ->join('branches', 'students.caid', '=', 'branches.ID')
        ->where('managefee.period', $periodId)
        ->select(
            'branches.branchname',
            DB::raw('COUNT(DISTINCT students.admno) as student_count'),
            DB::raw('SUM(managefee.amount) as total_expected'),
            DB::raw('SUM(managefee.paid) as total_collected'),
            DB::raw('SUM(managefee.balance) as total_pending')
        )
        ->groupBy('branches.ID', 'branches.branchname')
        ->get();

    $html = '<table>';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Branch/Campus</th>';
    $html .= '<th class="text-center">Students</th>';
    $html .= '<th class="text-right">Expected (KSh)</th>';
    $html .= '<th class="text-right">Collected (KSh)</th>';
    $html .= '<th class="text-right">Pending (KSh)</th>';
    $html .= '<th class="text-center">Collection Rate (%)</th>';
    $html .= '</tr>';
    $html .= '</thead><tbody>';

    foreach ($data as $row) {
        $rate = $row->total_expected > 0 ? round(($row->total_collected / $row->total_expected) * 100, 2) : 0;
        $html .= '<tr>';
        $html .= '<td><strong>' . $row->branchname . '</strong></td>';
        $html .= '<td class="text-center">' . number_format($row->student_count) . '</td>';
        $html .= '<td class="text-right">' . number_format($row->total_expected, 2) . '</td>';
        $html .= '<td class="text-right text-success">' . number_format($row->total_collected, 2) . '</td>';
        $html .= '<td class="text-right text-warning">' . number_format($row->total_pending, 2) . '</td>';
        $html .= '<td class="text-center">' . $rate . '%</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    return [
        'title' => 'Fee Collection Report by Branch/Campus',
        'html' => $html
    ];
}

private function getStudentReportForPDF($query)
{
    $data = $query->select(
        'students.admno',
        DB::raw("CONCAT(students.sirname, ' ', students.othername) as student_name"),
        'tblclasses.claname',
        'branches.branchname',
        DB::raw('SUM(managefee.amount) as total_expected'),
        DB::raw('SUM(managefee.paid) as total_collected'),
        DB::raw('SUM(managefee.balance) as total_pending')
    )
    ->groupBy('students.admno', 'student_name', 'tblclasses.claname', 'branches.branchname')
    ->orderBy('students.sirname')
    ->get();

    $html = '<table>';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Adm No</th>';
    $html .= '<th>Student Name</th>';
    $html .= '<th>Branch</th>';
    $html .= '<th>Class</th>';
    $html .= '<th class="text-right">Expected</th>';
    $html .= '<th class="text-right">Paid</th>';
    $html .= '<th class="text-right">Balance</th>';
    $html .= '<th class="text-center">Rate %</th>';
    $html .= '</tr>';
    $html .= '</thead><tbody>';

    foreach ($data as $row) {
        $rate = $row->total_expected > 0 ? round(($row->total_collected / $row->total_expected) * 100, 2) : 0;
        $rowClass = $row->total_pending <= 0 ? 'table-success' : ($rate > 50 ? 'table-info' : 'table-warning');
        
        $html .= '<tr class="' . $rowClass . '">';
        $html .= '<td>' . $row->admno . '</td>';
        $html .= '<td>' . $row->student_name . '</td>';
        $html .= '<td>' . $row->branchname . '</td>';
        $html .= '<td>' . $row->claname . '</td>';
        $html .= '<td class="text-right">' . number_format($row->total_expected, 2) . '</td>';
        $html .= '<td class="text-right text-success">' . number_format($row->total_collected, 2) . '</td>';
        $html .= '<td class="text-right text-danger">' . number_format($row->total_pending, 2) . '</td>';
        $html .= '<td class="text-center">' . $rate . '%</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    return [
        'title' => 'Fee Collection Report by Student',
        'html' => $html
    ];
}

private function getFeeItemReportForPDF($periodId, $branchId, $classId)
{
    $query = DB::table('managefee')
        ->join('students', 'managefee.admno', '=', 'students.admno')
        ->join('feeitems', 'managefee.feeid', '=', 'feeitems.ID')
        ->where('managefee.period', $periodId);

    if ($branchId) {
        $query->where('students.caid', $branchId);
    }

    if ($classId) {
        $query->where('students.claid', $classId);
    }

    $data = $query->select(
        'feeitems.feename',
        DB::raw('COUNT(DISTINCT students.admno) as student_count'),
        DB::raw('SUM(managefee.amount) as total_expected'),
        DB::raw('SUM(managefee.paid) as total_collected'),
        DB::raw('SUM(managefee.balance) as total_pending')
    )
    ->groupBy('feeitems.ID', 'feeitems.feename')
    ->get();

    $html = '<table>';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Fee Item</th>';
    $html .= '<th class="text-center">Students</th>';
    $html .= '<th class="text-right">Expected (KSh)</th>';
    $html .= '<th class="text-right">Collected (KSh)</th>';
    $html .= '<th class="text-right">Pending (KSh)</th>';
    $html .= '<th class="text-center">Collection Rate (%)</th>';
    $html .= '</tr>';
    $html .= '</thead><tbody>';

    foreach ($data as $row) {
        $rate = $row->total_expected > 0 ? round(($row->total_collected / $row->total_expected) * 100, 2) : 0;
        $html .= '<tr>';
        $html .= '<td><strong>' . $row->feename . '</strong></td>';
        $html .= '<td class="text-center">' . number_format($row->student_count) . '</td>';
        $html .= '<td class="text-right">' . number_format($row->total_expected, 2) . '</td>';
        $html .= '<td class="text-right text-success">' . number_format($row->total_collected, 2) . '</td>';
        $html .= '<td class="text-right text-warning">' . number_format($row->total_pending, 2) . '</td>';
        $html .= '<td class="text-center">' . $rate . '%</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    return [
        'title' => 'Fee Collection Report by Fee Item',
        'html' => $html
    ];
}

private function getDefaultersReportForPDF($periodId, $branchId, $classId)
{
    $query = DB::table('managefee')
        ->join('students', 'managefee.admno', '=', 'students.admno')
        ->join('tblclasses', 'students.claid', '=', 'tblclasses.ID')
        ->join('branches', 'students.caid', '=', 'branches.ID')
        ->where('managefee.period', $periodId)
        ->where('managefee.balance', '>', 0);

    if ($branchId) {
        $query->where('students.caid', $branchId);
    }

    if ($classId) {
        $query->where('students.claid', $classId);
    }

    $data = $query->select(
        'students.admno',
        DB::raw("CONCAT(students.sirname, ' ', students.othername) as student_name"),
        'tblclasses.claname',
        'branches.branchname',
        DB::raw('SUM(managefee.amount) as total_expected'),
        DB::raw('SUM(managefee.paid) as total_collected'),
        DB::raw('SUM(managefee.balance) as total_pending')
    )
    ->groupBy('students.admno', 'student_name', 'tblclasses.claname', 'branches.branchname')
    ->having('total_pending', '>', 0)
    ->orderBy('total_pending', 'desc')
    ->get();

    $html = '<div class="alert alert-warning"><strong>⚠ Warning:</strong> Students with outstanding balances - immediate follow-up recommended.</div>';
    $html .= '<table>';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Adm No</th>';
    $html .= '<th>Student Name</th>';
    $html .= '<th>Branch</th>';
    $html .= '<th>Class</th>';
    $html .= '<th class="text-right">Expected</th>';
    $html .= '<th class="text-right">Paid</th>';
    $html .= '<th class="text-right">Outstanding</th>';
    $html .= '<th class="text-center">% Paid</th>';
    $html .= '</tr>';
    $html .= '</thead><tbody>';

    foreach ($data as $row) {
        $percentPaid = $row->total_expected > 0 ? round(($row->total_collected / $row->total_expected) * 100, 2) : 0;
        $html .= '<tr>';
        $html .= '<td>' . $row->admno . '</td>';
        $html .= '<td><strong>' . $row->student_name . '</strong></td>';
        $html .= '<td>' . $row->branchname . '</td>';
        $html .= '<td>' . $row->claname . '</td>';
        $html .= '<td class="text-right">' . number_format($row->total_expected, 2) . '</td>';
        $html .= '<td class="text-right text-success">' . number_format($row->total_collected, 2) . '</td>';
        $html .= '<td class="text-right text-danger"><strong>' . number_format($row->total_pending, 2) . '</strong></td>';
        $html .= '<td class="text-center">' . $percentPaid . '%</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    return [
        'title' => 'Fee Defaulters Report',
        'html' => $html
    ];
}

private function getOverpaymentReportForPDF($periodId, $branchId, $classId)
{
    $query = DB::table('managefee')
        ->join('students', 'managefee.admno', '=', 'students.admno')
        ->join('tblclasses', 'students.claid', '=', 'tblclasses.ID')
        ->join('branches', 'students.caid', '=', 'branches.ID')
        ->where('managefee.period', $periodId)
        ->whereRaw('managefee.paid > managefee.amount');

    if ($branchId) {
        $query->where('students.caid', $branchId);
    }

    if ($classId) {
        $query->where('students.claid', $classId);
    }

    $data = $query->select(
        'students.admno',
        DB::raw("CONCAT(students.sirname, ' ', students.othername) as student_name"),
        'tblclasses.claname',
        'branches.branchname',
        'managefee.amount',
        'managefee.paid',
        DB::raw('(managefee.paid - managefee.amount) as overpayment')
    )
    ->orderBy('overpayment', 'desc')
    ->get();

    $html = '<div class="alert alert-info"><strong>ℹ Information:</strong> Overpayments should be verified and either refunded or applied to future periods.</div>';
    $html .= '<table>';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th>Adm No</th>';
    $html .= '<th>Student Name</th>';
    $html .= '<th>Branch</th>';
    $html .= '<th>Class</th>';
    $html .= '<th class="text-right">Expected</th>';
    $html .= '<th class="text-right">Paid</th>';
    $html .= '<th class="text-right">Overpayment</th>';
    $html .= '</tr>';
    $html .= '</thead><tbody>';

    if ($data->isEmpty()) {
        $html .= '<tr><td colspan="7" class="text-center">No overpayments found</td></tr>';
    } else {
        foreach ($data as $row) {
            $html .= '<tr>';
            $html .= '<td>' . $row->admno . '</td>';
            $html .= '<td><strong>' . $row->student_name . '</strong></td>';
            $html .= '<td>' . $row->branchname . '</td>';
            $html .= '<td>' . $row->claname . '</td>';
            $html .= '<td class="text-right">' . number_format($row->amount, 2) . '</td>';
            $html .= '<td class="text-right">' . number_format($row->paid, 2) . '</td>';
            $html .= '<td class="text-right text-danger"><strong>' . number_format($row->overpayment, 2) . '</strong></td>';
            $html .= '</tr>';
        }
    }

    $html .= '</tbody></table>';

    return [
        'title' => 'Overpayment Report',
        'html' => $html
    ];
}

private function exportToExcel(Request $request)
{
    // Similar to student export - create CSV
    $reportType = $request->input('report_type', 'summary');
    $periodId = $request->input('period');
    
    $filename = 'fee_report_' . $reportType . '_' . date('Y-m-d_His') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function() use ($request, $reportType, $periodId) {
        $file = fopen('php://output', 'w');
        
        // Add appropriate headers based on report type
        // Implementation similar to PDF but for CSV format
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

private function exportToPrint(Request $request)
{
    // Return a print-friendly view
    return $this->exportToPDF($request);
}
}