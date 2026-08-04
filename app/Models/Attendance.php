<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';
    protected $fillable = [
        'date',
        'staff_id',
        'check_in',
        'check_out',
        'late_minutes',
        'early_leave_minutes',
        'working_minutes',
        'overtime_minutes',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    // public function staff()
    // {
    //     return $this->belongsTo(Staff::class, 'staff_id');
    // }
}
