<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;

class AttendanceLogController extends Controller
{
    public function index()
    {
        $logs = AttendanceLog::with('staff')->get();

        return response()->json([
            'logs' => $logs
        ]);
    }
}
