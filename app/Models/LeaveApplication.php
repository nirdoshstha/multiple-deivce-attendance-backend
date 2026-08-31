<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class LeaveApplication extends Model
{
    protected $table = "leave_applications";
    protected $fillable = [
        'user_id',
        'role_id',
        'leave_type_id',
        'date_from',
        'date_to',
        'total_days',
        'day_type',
        'reason',
        'is_approved',
        'approved_by',
        'approval_authorized_user',
        'approval_authorized_by',
        'approved_at',
        'approval_remarks',
        'created_by'
    ];

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function leave_type()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'id');
    }
}
