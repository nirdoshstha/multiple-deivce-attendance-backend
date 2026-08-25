<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Calendar;
use App\Models\Holiday;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Krbaidik\AdBsConverter\Facades\NepaliDate;

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


    // public function index()
    // {
    //     $holidays = Holiday::whereMonth('date', $month)
    //         ->whereYear('date', $year)
    //         ->get();

    //     return response()->json([
    //         'holidays' => $holidays,
    //     ]);
    // }

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
                $officeTime = Carbon::parse($attendance['date'] . ' 09:00');
                $officeExitTime = Carbon::parse($attendance['date'] . ' 17:00');

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

                $record = Attendance::where(

                    [
                        'staff_id' => $attendance['staff_id'],
                        'date' => $date,
                    ]
                )
                    ->first();

                $attendanceRecord = Attendance::updateOrCreate(

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

                $changedFields = $attendanceRecord->getChanges();

                $changedAttendance = collect($changedFields)
                    ->only(['check_in', 'check_out'])
                    ->toArray();

                foreach ($changedAttendance as $field => $val) {
                    AttendanceLog::create([
                        'staff_id' => $attendance['staff_id'],
                        'date' => $date,
                        'punch_time' => $attendance[$field],
                        'verification_type' => 'manual',
                        'punch_type' => $field,

                        'raw_data' => json_encode([
                            'source' => 'web',
                            'attendance_id' => $attendanceRecord->id,
                        ]),

                        'created_by' => auth()->id(),
                    ]);
                }


                // Check In Log
                // if ($record?->check_in != $attendanceRecord->check_in) {

                //     AttendanceLog::create([
                //         'staff_id' => $attendance['staff_id'],
                //         'date' => $date,
                //         'punch_time' => $attendance['check_in'],
                //         'verification_type' => 'manual',
                //         'punch_type' => 'check_in',

                //         'raw_data' => json_encode([
                //             'source' => 'web',
                //             'attendance_id' => $attendanceRecord->id,
                //         ]),

                //         'created_by' => auth()->id(),
                //     ]);
                // }

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

    // public function searchByDate(Request $request)
    // {
    //     $date  = $request->input('date');   // e.g. "2083-04-16"  (full BS date, daily view)
    //     $month = $request->input('month');  // e.g. 4            (monthly view)
    //     $year  = $request->input('year');   // e.g. 2083          (monthly view)

    //     $user = auth()->user();

    //     $query = Staff::with([
    //         'company',
    //         'designation',
    //         'attendances' => function ($q) use ($date, $month, $year) {

    //             if ($date) {
    //                 // ---- Daily search: exact BS date match ----
    //                 $q->where('date', $date);
    //             } elseif ($month && $year) {

    //                 $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
    //                 $q->where('date', 'like', "{$year}-{$monthStr}-%");
    //             }

    //             $q->orderBy('date');
    //         }
    //     ]);

    //     if (!$user->can('staffs.view.all')) {
    //         $query->whereIn('company_id', $user->companies->pluck('id'));
    //     }

    //     $staffs = $query->get();
    //     $holidays = Calendar::where('is_holiday', 1)->get();

    //     return response()->json([
    //         'status' => 200,
    //         'staffs' => $staffs, 
    //         'holidays' => $holidays,
    //     ]);
    // }

    public function searchByDate(Request $request)
    {
        $user = auth()->user();

        $month = str_pad($request->month, 2, '0', STR_PAD_LEFT);
        $date = $request->year . '-' . $month . '-01';

        $query = Staff::with([
            'company',
            'designation',
            'attendances' => function ($q) use ($request) {

                // Daily Search
                if ($request->date) {
                    $q->where('date', $request->date);
                }


                // Monthly Search
                if ($request->year && $request->month) {
                    $month = str_pad($request->month, 2, '0', STR_PAD_LEFT);

                    $q->where('date', 'like', $request->year . '-' . $month . '-%');
                }

                $q->orderBy('date');
            }
        ]);

        // Company-wise permission
        if (!$user->can('staffs.view.all')) {
            $query->whereIn('company_id', $user->companies->pluck('id'));
        }

        $staffs = $query->get();

        // $totalDays = 32; // Get this from your BS calendar library
        // $days = range(1, $totalDays);
        // $month = str_pad($request->month, 2, '0', STR_PAD_LEFT);


        //Total days in a month of a year real days will be come
        $totalDays = NepaliDate::getDaysInMonth($request->year, $request->month); // 31
        $days = range(1, $totalDays);

        $holidays = Calendar::where('is_holiday', 1)
            ->where('date', 'like', $request->year . '-' . $month . '-%')
            ->get();

        $holiday_date = Calendar::where('is_holiday', 1)
            ->where('date', $request->date)
            ->exists();

        return response()->json([
            'status' => 200,
            'staffs' => $staffs,
            'date' => $request->date,
            'days' => $days,
            'holidays' => $holidays,
            'disabled' => $holiday_date
        ]);
    }
}
