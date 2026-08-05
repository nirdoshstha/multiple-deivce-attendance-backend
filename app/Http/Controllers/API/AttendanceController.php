<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{

    // public function index()
    // {
    //     // $companyId = auth()->user()->companies->pluck('id')->first(); // Get the first company ID associated with the authenticated user
    //     // $staffs = Staff::with('company', 'designation')->where('company_id', $companyId)->get();


    //     $companyId = auth()->user();
    //     if ($companyId->can('staffs.view.all')) {
    //         $staffs = Staff::with('company', 'designation')->get();
    //     } else {
    //         $staffs = Staff::with('company', 'designation')
    //             ->whereIn('company_id', $companyId->companies->pluck('id'))
    //             ->get();
    //     }
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Staffs retrieved successfully',
    //         'staffs' => $staffs
    //     ]);
    // }

    // public function index()
    // {
    //     $today = now()->toDateString();

    //     $user = auth()->user();

    //     if ($user->can('staffs.view.all')) {

    //         $staffs = Staff::with([
    //             'company',
    //             'designation',
    //             'attendance' => function ($q) use ($today) {
    //                 $q->whereDate('date', $today);
    //             }
    //         ])->get();
    //     } else {

    //         $staffs = Staff::with([
    //             'company',
    //             'designation',
    //             'attendance' => function ($q) use ($today) {
    //                 $q->whereDate('date', $today);
    //             }
    //         ])
    //             ->whereIn('company_id', $user->companies->pluck('id'))
    //             ->get();
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'staffs' => $staffs
    //     ]);
    // }


    public function index()
    {
        $holidays = Holiday::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        return response()->json([
            'holidays' => $holidays,
        ]);
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $date = $request->input('date') ?? now()->toDateString();


        $request->validate([

            'attendances.*.check_in' => 'nullable',
            'attendances.*.check_out' => 'nullable',
            'attendances.*.remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {

            foreach ($request->attendances as $attendance) {

                $date = !empty($attendance['date'])
                    ? $attendance['date']
                    : now()->toDateString();

                // Office timings
                $officeTime = Carbon::parse($attendance['date'] . ' 09:00:00');
                $officeExitTime = Carbon::parse($attendance['date'] . ' 17:00:00');

                // Check In
                $checkIn = !empty($attendance['check_in'])
                    ? Carbon::parse($attendance['date'] . ' ' . $attendance['check_in'])
                    : null;

                // Check Out
                $checkOut = !empty($attendance['check_out'])
                    ? Carbon::parse($attendance['date'] . ' ' . $attendance['check_out'])
                    : null;

                // Late Minutes
                $lateMinutes = 0;

                if ($checkIn && $checkIn->gt($officeTime)) {
                    $lateMinutes = $officeTime->diffInMinutes($checkIn);
                }

                // Early Leave
                $earlyLeaveMinutes = 0;

                if ($checkOut && $checkOut->lt($officeExitTime)) {
                    $earlyLeaveMinutes = $checkOut->diffInMinutes($officeExitTime);
                }

                // Overtime Leave
                $overtimeMinutes = 0;

                if ($checkOut && $checkOut->gt($officeExitTime)) {
                    $overtimeMinutes = $checkOut->diffInMinutes($officeExitTime);
                }

                // Working Minutes
                $workingMinutes = 0;

                if ($checkIn && $checkOut) {
                    $workingMinutes = $checkIn->diffInMinutes($checkOut);
                }

                Attendance::updateOrCreate(

                    [
                        'staff_id' => $attendance['staff_id'],
                        'date' => $date,
                    ],

                    [
                        'check_in' => $attendance['check_in'],
                        'check_out' => $attendance['check_out'],
                        'remarks' => $attendance['remarks'] ?? null,
                        'late_minutes' => $lateMinutes,
                        'early_leave_minutes' => $earlyLeaveMinutes,
                        'working_minutes' => $workingMinutes,
                        'overtime_minutes' => $overtimeMinutes,
                        'status' => $attendance['status'] ?? 'absent',
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Attendance saved successfully.'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // public function searchByDate(Request $request)
    // {
    //     $date = $request->input('date') ?? now()->toDateString(); // Default to today if no date is provided


    //     // $month = $request->month ?? now()->month;
    //     $user = auth()->user();

    //     $query = Staff::with([
    //         'company',
    //         'designation',
    //         'attendances' => function ($q) use ($date) {
    //             $q->whereBetween('date', [$date, $date]);
    //         }
    //     ]);

    //     if (!$user->can('staffs.view.all')) {
    //         $query->whereIn('company_id', $user->companies->pluck('id'));
    //     }

    //     $staffs = $query->get();
    //     return response()->json([
    //         'status' => 200,
    //         'staffs' => $staffs
    //     ]);
    // } 

    public function searchByDate(Request $request)
    {
        $date  = $request->input('date');   // e.g. "2083-04-16"  (full BS date, daily view)
        $month = $request->input('month');  // e.g. 4            (monthly view)
        $year  = $request->input('year');   // e.g. 2083          (monthly view)

        $user = auth()->user();

        $query = Staff::with([
            'company',
            'designation',
            'attendances' => function ($q) use ($date, $month, $year) {

                if ($date) {
                    // ---- Daily search: exact BS date match ----
                    $q->where('date', $date);
                } elseif ($month && $year) {
                    // ---- Monthly search ----
                    // Dates are stored as plain "YYYY-MM-DD" Nepali (BS) strings,
                    // so we can't use whereMonth()/whereYear() (those assume
                    // MySQL can parse the value as a real/Gregorian date, which
                    // it can't for BS years like 2083). A string LIKE match on
                    // the year-month prefix works reliably instead.
                    $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
                    $q->where('date', 'like', "{$year}-{$monthStr}-%");
                }
                // If neither is provided, no filter is applied and it will
                // return ALL attendance rows for each staff — guard against
                // that on the frontend by always sending one of the two.

                $q->orderBy('date');
            }
        ]);

        if (!$user->can('staffs.view.all')) {
            $query->whereIn('company_id', $user->companies->pluck('id'));
        }

        $staffs = $query->get();
        $holidays = Holiday::whereMonth('nepali_date', $month)
            ->whereYear('nepali_date', $year)
            ->get();

        return response()->json([
            'status' => 200,
            'staffs' => $staffs,
            'holidays' => $holidays,
        ]);
    }
}
