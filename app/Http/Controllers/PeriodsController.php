<?php

namespace App\Http\Controllers;

use App\Models\Periods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class PeriodsController extends Controller
{
    public function create()
{
    $periods = Periods::distinct()->get(['ID', 'periodname', 'pstatus', 'startdate', 'enddate']);
    dd($periods); // Debug data
    return view('students.static', compact('periods'));
}


    public function store(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'periodname' => 'required|string|max:255',
        'pstatus' => 'required|string|max:255',
        'startdate' => 'required|string|max:255',
        'enddate' => 'required|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    // Insert into the database
    Periods::create([
        'periodname' => $request->periodname,
        'pstatus' => $request->pstatus,
        'startdate' => $request->startdate,
        'enddate' => $request->enddate,
    ]);

    return response()->json([
        'message' => 'Period Saved!',
    ]);
}

public function store2(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'periodname' => 'required|string|max:255',
        'pstatus' => 'required|string|in:Active,Inactive',
        'startdate' => 'required|date',
        'enddate' => 'required|date|after:startdate',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'errors' => $validator->errors(),
        ], 422);
    }

    try {
        // If creating an active period, deactivate all other periods
        if ($request->pstatus === 'Active') {
            Periods::where('pstatus', 'Active')->update(['pstatus' => 'Inactive']);
        }

        // Create the new period
        Periods::create([
            'periodname' => $request->periodname,
            'pstatus' => $request->pstatus,
            'startdate' => $request->startdate,
            'enddate' => $request->enddate,
        ]);

        return response()->json([
            'message' => 'Period created successfully!',
        ]);
    } catch (\Exception $e) {
        Log::error('Period creation error: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'Failed to create period: ' . $e->getMessage()
        ], 500);
    }
}

public function getAll()
{
    $periods = Periods::paginate(3); // 3 records per page

    // Format each period's start and end date
    $formattedData = $periods->map(function ($period) {
        return [
            'ID' => $period->ID,
            'periodname' => $period->periodname,
            'pstatus' => $period->pstatus,
            //'branchname' => $period->branchname ?? '', // Add if needed
            'startdate' => $period->startdate,
            'enddate' => $period->enddate,
            'startdate_formatted' => Carbon::parse($period->startdate)->format('d/M/Y'),
            'enddate_formatted' => Carbon::parse($period->enddate)->format('d/M/Y'),
        ];
    });

    return response()->json([
        'data' => $formattedData,
        'pagination' => [
            'current_page' => $periods->currentPage(),
            'last_page' => $periods->lastPage(),
            'per_page' => $periods->perPage(),
            'total' => $periods->total(),
        ],
    ]);
}

public function getCurrentPeriod()
{
    $today = Carbon::today();

    $period = Periods::where('pstatus', 'Active')
        ->whereDate('startdate', '<=', $today)
        ->whereDate('enddate', '>=', $today)
        ->first();

    if ($period) {
        return response()->json([
            'status' => 'success',
            'periodname' => $period->periodname,
            'enddate_formatted' => Carbon::parse($period->enddate)->format('d/M/Y')
        ]);
    } else {
        return response()->json([
            'status' => 'none',
            'message' => 'No active period'
        ]);
    }
}


}

