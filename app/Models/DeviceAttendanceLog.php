<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceAttendanceLog extends Model
{
    protected $fillable = [
        'company_device_id',
        'staff_id',
        'device_user_id',
        'punch_time',
        'verify_type',
        'punch_state',
        'processed',
    ];

    protected $casts = [
        'punch_time' => 'datetime',
        'processed' => 'boolean',
    ];

    public function companyDevice()
    {
        return $this->belongsTo(CompanyDevice::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
