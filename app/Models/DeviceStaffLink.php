<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceStaffLink extends Model
{
    protected $fillable = [
        'company_device_id',
        'staff_id',
        'device_user_id',
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
