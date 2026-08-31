<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends BackendBaseModel
{
    use SoftDeletes;

    protected $table = 'staffs';
    protected $fillable = [
        'name',
        'company_id',
        'phone',
        'image',
        'email',
        'user_id',
        'gender',
        'designation_id',
        'address',
        'working_hr',
        'status',
        'created_by',
        'updated_by'
    ];

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }


    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

   public function leaves() {
    return $this->hasMany(LeaveApplication::class, 'user_id', 'user_id');
    }
    public function leave_type()
        {
            return $this->belongsTo(LeaveType::class, 'leave_type_id', 'id');
        }
}
