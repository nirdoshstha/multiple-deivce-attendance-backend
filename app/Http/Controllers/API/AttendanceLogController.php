<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeviceAttendanceLog;
use Illuminate\Http\Request;

class AttendanceLogController extends Controller
{
    public function index()
    {
        $logs = DeviceAttendanceLog::with([
            'staff.user:id,image',
        ])->get();

        return response()->json([
            'logs' => $logs
        ]);
    }
}
