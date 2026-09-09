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
        ]);

        $link = DeviceStaffLink::updateOrCreate(
            [
                'staff_id' => $validated['staff_id'],
                'company_device_id' => $validated['company_device_id'],
            ],
            [
                'device_user_id' => $validated['device_user_id'],
            ]
        );

        return response()->json([
            'message' => 'Biometric ID updated successfully.',
            'data' => $link,
        ]);
    }
}
