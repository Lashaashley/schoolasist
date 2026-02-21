<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teachers;
use App\Models\Managefee;
use App\Models\Perfomance;
use App\Models\Classes;
use App\Models\Subjects;
use App\Models\Periods;
use App\Models\Branches;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getDashboardData()
    {
        try {
            // Get active period
            $activePeriod = Periods::where('pstatus', 'Active')->first();
            $periodId = $activePeriod ? $activePeriod->ID : null;

            // 1. Total Students
            $totalStudents = Student::count();

            // 2. Total Teachers
            $totalTeachers = Teachers::count();

            // 3. Revenue Analytics (for current period)
            $revenueProjected = Managefee::where('period', $periodId)->sum('amount');
            $revenueCollected = Managefee::where('period', $periodId)->sum('paid');
            $revenuePending = Managefee::where('period', $periodId)->sum('balance');

            // 4. Students by Gender
            $studentsByGender = [
                [
                    'name' => 'Male',
                    'y' => Student::where('gender', 'male')->count(),
                    'color' => '#3498db'
                ],
                [
                    'name' => 'Female',
                    'y' => Student::where('gender', 'female')->count(),
                    'color' => '#e74c3c'
                ]
            ];

            // 5. Students by Class
            $studentsByClass = DB::table('students')
                ->join('tblclasses', 'students.claid', '=', 'tblclasses.ID')
                ->select('tblclasses.claname as name', DB::raw('COUNT(students.StudentID) as y'))
                ->groupBy('tblclasses.ID', 'tblclasses.claname')
                ->orderBy('tblclasses.clarank')
                ->get()
                ->toArray();

            // 6. Fee Collection Status
            $feeCollectionStatus = [
                [
                    'name' => 'Paid',
                    'y' => floatval($revenueCollected),
                    'color' => '#27ae60'
                ],
                [
                    'name' => 'Pending',
                    'y' => floatval($revenuePending),
                    'color' => '#f39c12'
                ]
            ];

            // 7. Subject Performance (by period)
            $allPeriods = Periods::orderBy('ID', 'desc')->get();
            $subjectPerformance = [];

            foreach ($allPeriods as $period) {
                $performance = DB::table('performancetbl')
                    ->join('tblsubjects', 'performancetbl.subid', '=', 'tblsubjects.ID')
                    ->where('performancetbl.examperiod', $period->ID)
                    ->select('tblsubjects.sname as name', DB::raw('AVG(performancetbl.marks) as y'))
                    ->groupBy('tblsubjects.ID', 'tblsubjects.sname')
                    ->orderBy('tblsubjects.sname')
                    ->get()
                    ->map(function($item) {
                        return [
                            'name' => $item->name,
                            'y' => round(floatval($item->y), 2)
                        ];
                    })
                    ->toArray();

                $subjectPerformance[$period->ID] = $performance;
            }

            // 8. Class Performance
            $classPerformance = DB::table('performancetbl')
                ->join('tblclasses', 'performancetbl.classid', '=', 'tblclasses.ID')
                ->where('performancetbl.examperiod', $periodId)
                ->select('tblclasses.claname as name', DB::raw('AVG(performancetbl.marks) as y'))
                ->groupBy('tblclasses.ID', 'tblclasses.claname')
                ->orderBy('y', 'desc')
                ->limit(10)
                ->get()
                ->map(function($item) {
                    return [
                        'name' => $item->name,
                        'y' => round(floatval($item->y), 2)
                    ];
                })
                ->toArray();

            // 9. Boarding vs Day Scholars
            $boardingDistribution = [
                [
                    'name' => 'Boarders',
                    'y' => Student::where('border', 'yes')->count(),
                    'color' => '#9b59b6'
                ],
                [
                    'name' => 'Day Scholars',
                    'y' => Student::where('border', 'no')->count(),
                    'color' => '#1abc9c'
                ]
            ];

            // 10. Recent Payments (last 10)
            $recentPayments = DB::table('managefee')
                ->join('students', 'managefee.admno', '=', 'students.admno')
                ->join('tblclasses', 'students.claid', '=', 'tblclasses.ID')
                ->where('managefee.paid', '>', 0)
                ->where('managefee.period', $periodId)
                ->select(
                    'students.admno',
                    DB::raw("CONCAT(students.sirname, ' ', students.othername) as student_name"),
                    'tblclasses.claname as class_name',
                    'managefee.paid',
                    'managefee.balance',
                    'managefee.updated_at'
                )
                ->orderBy('managefee.updated_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($payment) {
                    return [
                        'admno' => $payment->admno,
                        'student_name' => $payment->student_name,
                        'class_name' => $payment->class_name,
                        'paid' => floatval($payment->paid),
                        'balance' => floatval($payment->balance),
                        'date' => Carbon::parse($payment->updated_at)->format('d M Y')
                    ];
                })
                ->toArray();

            return response()->json([
                'totalStudents' => $totalStudents,
                'totalTeachers' => $totalTeachers,
                'revenueProjected' => floatval($revenueProjected),
                'revenueCollected' => floatval($revenueCollected),
                'revenuePending' => floatval($revenuePending),
                'studentsByGender' => $studentsByGender,
                'studentsByClass' => $studentsByClass,
                'feeCollectionStatus' => $feeCollectionStatus,
                'subjectPerformance' => $subjectPerformance,
                'classPerformance' => $classPerformance,
                'boardingDistribution' => $boardingDistribution,
                'recentPayments' => $recentPayments,
                'periods' => $allPeriods,
                'currentPeriod' => $activePeriod
            ]);

        } catch (\Exception $e) {
            Log::error('Dashboard data error: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to load dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }
}