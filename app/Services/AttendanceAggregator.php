<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\DeviceAttendanceLog;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Krbaidik\AdBsConverter\Facades\NepaliDate;

class AttendanceAggregator
{
    /**
     * Rebuild the aggregated Attendance row for one staff member on one date
     * from every stored punch that day: earliest punch = check_in, latest =
     * check_out. Wrapped in a transaction + row lock so overlapping callers
     * (a scheduled pull-sync and a live device push arriving at the same
     * moment) can't corrupt it.
     */
    // public function rebuildFor(int $staffId, string $date, ?int $companyDeviceId = null): void
    // {

    //     DB::transaction(function () use ($staffId, $date, $companyDeviceId) {
    //         $punches = DeviceAttendanceLog::query()
    //             ->where('staff_id', $staffId)
    //             ->whereDate('punch_time', $date)
    //             ->orderBy('punch_time')
    //             ->get();


    //         if ($punches->isEmpty()) {
    //             return;
    //         }

    //         $explodeDate = explode('-', $date); //for convert eng to nepali;

    //         $nepDate = NepaliDate::engToNep($explodeDate[0], $explodeDate[1], $explodeDate[2]);
    //         $formatedNepaliDate = $nepDate['year'] . '-' . str_pad($nepDate['month'], 2, 0, STR_PAD_LEFT) . '-' . $nepDate['date'];

    //         $checkIn = $punches->first()->punch_time;
    //         $checkOut = $punches->count() > 1 ? $punches->last()->punch_time : null;

    //         $officeTime = Carbon::parse('09:00');
    //         $officeExitTime = Carbon::parse('17:00');

    //         $lateMinutes = 0;
    //         $earlyLeaveMinutes = 0;
    //         $overTimeMinutes = 0;

    //         if ($checkIn) {
    //             $lateMinutes = $checkIn->greaterThan($officeTime)
    //                 ? $officeTime->diffInMinutes($checkIn)
    //                 : 0;
    //         }

    //         if ($checkOut) {
    //             $earlyLeaveMinutes = $checkOut->lessThan($officeExitTime)
    //                 ? $checkOut->diffInMinutes($officeExitTime)
    //                 : 0;
    //         }

    //         if ($checkOut) {
    //             $overTimeMinutes = $checkOut->greaterThan($officeExitTime)
    //                 ? $officeExitTime->diffInMinutes($checkOut)
    //                 : 0;
    //         }


    //         // $staff = Staff::find($staffId);
    //         $attendance = Attendance::where('staff_id', $staffId)
    //             ->where('date', $formatedNepaliDate)
    //             ->first();

    //         if ($attendance) {
    //             $checkIn = $attendance->check_in;
    //         } else {
    //             $checkIn = $punches->first()->punch_time;
    //         }

    //         // $attendance->updateOrCreate(
    //         //     [
    //         //         'staff_id' => $staffId,
    //         //         'date' => $formatedNepaliDate
    //         //     ],
    //         //     [
    //         //         'company_id' => $staff->company_id,
    //         //         'check_in' => $checkIn,
    //         //         'check_out' => $checkOut,
    //         //         'late_minutes' => $lateMinutes,
    //         //         'early_leave_minutes' => $earlyLeaveMinutes,
    //         //         'working_minutes' => $checkOut ? $checkIn->diffInMinutes($checkOut) : 0,
    //         //         'over_time_minutes' => $overTimeMinutes,
    //         //         'status' => 'present',
    //         //         // late_minutes / early_leave_minutes / overtime_minutes depend on
    //         //         // each staff member's shift schedule — compute those in a
    //         //         // dedicated ShiftCalculator once you have shift data.
    //         //     ]
    //         // );

    //         $attendance->update([
    //             // NO check_in here
    //             'check_out' => $checkOut,
    //             'late_minutes' => $lateMinutes,
    //             'early_leave_minutes' => $earlyLeaveMinutes,
    //             'working_minutes' => $checkOut ? $checkIn->diffInMinutes($checkOut) : 0,
    //             'over_time_minutes' => $overTimeMinutes,
    //         ]);
    //         DeviceAttendanceLog::query()->whereIn('id', $punches->pluck('id'))->update(['processed' => true]);
    //     });
    // }

    public function rebuildFor(
        int $staffId,
        string $date,
        ?int $companyDeviceId = null
    ): void {

        DB::transaction(function () use ($staffId, $date, $companyDeviceId) {

            $punches = DeviceAttendanceLog::query()
                ->where('staff_id', $staffId)
                ->where('date', $date)
                ->orderBy('time')
                ->get();

            if ($punches->isEmpty()) {
                return;
            }

            /*
        |--------------------------------------------------------------------------
        | Staff
        |--------------------------------------------------------------------------
        */

            $staff = Staff::findOrFail($staffId);


            /*
        |--------------------------------------------------------------------------
        | Punch times
        |--------------------------------------------------------------------------
        */

            $firstPunch = $punches->first()->time;

            $lastPunch = $punches->count() > 1
                ? $punches->last()->time
                : null;


            /*
        |--------------------------------------------------------------------------
        | Find existing attendance
        |--------------------------------------------------------------------------
        */

            $attendance = Attendance::where('staff_id', $staffId)
                ->where('date', $date)
                ->first();


            /*
        |--------------------------------------------------------------------------
        | Check-in
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Once check_in exists, don't overwrite it.
        |
        */

            if (!$attendance) {

                $checkIn = $firstPunch;
            } else {

                $checkIn = $attendance->check_in;
            }


            /*
        |--------------------------------------------------------------------------
        | Check-out
        |--------------------------------------------------------------------------
        |
        | Only update checkout when there is more than one punch.
        |
        */

            $checkOut = $attendance?->check_out;

            if ($lastPunch) {
                $checkOut = $lastPunch;
            }


            /*
        |--------------------------------------------------------------------------
        | Office times
        |--------------------------------------------------------------------------
        */

            $device_link = $staff->device_link;
            $officeTime = Carbon::parse($device_link?->duty_start_time);
            $officeExitTime = Carbon::parse($device_link?->duty_end_time);


            /*
        |--------------------------------------------------------------------------
        | Late minutes
        |--------------------------------------------------------------------------
        */

            $lateMinutes = 0;

            if ($checkIn) {

                $checkInCarbon = Carbon::parse($checkIn);

                $lateMinutes = $checkInCarbon->greaterThan($officeTime)
                    ? $officeTime->diffInMinutes($checkInCarbon)
                    : 0;
            }


            /*
        |--------------------------------------------------------------------------
        | Early leave
        |--------------------------------------------------------------------------
        */

            $earlyLeaveMinutes = 0;

            if ($checkOut) {

                $checkOutCarbon = Carbon::parse($checkOut);

                $earlyLeaveMinutes = $checkOutCarbon->lessThan($officeExitTime)
                    ? $checkOutCarbon->diffInMinutes($officeExitTime)
                    : 0;
            }


            /*
        |--------------------------------------------------------------------------
        | Overtime
        |--------------------------------------------------------------------------
        */

            $overTimeMinutes = 0;

            if ($checkOut) {

                $checkOutCarbon = Carbon::parse($checkOut);

                $overTimeMinutes = $checkOutCarbon->greaterThan($officeExitTime)
                    ? $officeExitTime->diffInMinutes($checkOutCarbon)
                    : 0;
            }


            /*
        |--------------------------------------------------------------------------
        | Working minutes
        |--------------------------------------------------------------------------
        */

            $workingMinutes = 0;

            if ($checkIn && $checkOut) {

                $workingMinutes = Carbon::parse($checkIn)
                    ->diffInMinutes(Carbon::parse($checkOut));
            }




            /*
        |--------------------------------------------------------------------------
        | Save attendance
        |--------------------------------------------------------------------------
        */

            if ($attendance) {

                // Existing record:
                // DO NOT touch check_in

                $attendance->update([
                    'company_id' => $staff->company_id,
                    'check_out' => $checkOut,
                    'late_minutes' => $lateMinutes,
                    'early_leave_minutes' => $earlyLeaveMinutes,
                    'working_minutes' => $workingMinutes,
                    'over_time_minutes' => $overTimeMinutes,
                    'status' => 'present',
                ]);
            } else {

                // First punch: create check-in

                Attendance::create([
                    'staff_id' => $staffId,
                    'company_id' => $staff->company_id,
                    'date' => $date,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'late_minutes' => $lateMinutes,
                    'early_leave_minutes' => $earlyLeaveMinutes,
                    'working_minutes' => $workingMinutes,
                    'over_time_minutes' => $overTimeMinutes,
                    'status' => 'present',
                ]);
            }


            /*
        |--------------------------------------------------------------------------
        | Mark device punches processed
        |--------------------------------------------------------------------------
        */

            DeviceAttendanceLog::query()
                ->whereIn('id', $punches->pluck('id'))
                ->update([
                    'processed' => true
                ]);
        });
    }
}
