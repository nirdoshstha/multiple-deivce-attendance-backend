<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends BackendBaseModel
{
    use SoftDeletes;


    protected $table = "leave_types";
    protected $fillable = [
        'name',
        'slug',
        'days_per_year',
        'is_paid',
        'requires_approval',
        'allow_half_day',
        'status',
        'created_by',
        'updated_by'
    ];
}
