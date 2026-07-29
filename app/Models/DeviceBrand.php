<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceBrand extends Model
{
    protected $table = 'device_brands';
    protected $fillable = [
        'name',
        'slug',
        'website',
        'status',
        'created_by',
        'updated_by'
    ];
}
