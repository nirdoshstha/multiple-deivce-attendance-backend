<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeviceStaffLink;
use Illuminate\Http\Request;

class StaffDeviceLinkController extends Controller
{

    public function store(Request $request)
    {

        $validated = $request->validate([
            'staff_id' => 'required|exists:staffs,id',
            'company_device_id' => 'required|exists:companies_devices,id',
            'device_user_id' => 'required|string|max:100',
            'duty_start_time' => 'nullable|date_format:H:i',
            'duty_end_time' => 'nullable|date_format:H:i',
        ]);

        $link = DeviceStaffLink::updateOrCreate(
            [
                'staff_id' => $validated['staff_id'],
                'company_device_id' => $validated['company_device_id'],
            ],
            [
                'device_user_id' => $validated['device_user_id'],
                'duty_start_time' => $validated['duty_start_time'] ?? null,
                'duty_end_time' => $validated['duty_end_time'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'Biometric ID updated successfully.',
            'data' => $link,
        ]);
    }
}
