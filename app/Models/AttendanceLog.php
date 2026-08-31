<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends BackendBaseModel
{
    protected $table = "attendance_logs";
    protected $fillable = [
        'staff_id',
        'date',
        'punch_time',
        'punch_type',
        'verification_type',
        'raw_data',
        'status',
        'created_by',
        'updated_by',

    ];

    protected $casts = [
        'raw_data' => 'array',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id', 'id');
    }

    public function companyDevice()
    {
        return $this->belongsTo(CompanyDevice::class);
    }
}
