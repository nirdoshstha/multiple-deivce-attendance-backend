<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $table = 'devices';
    protected $fillable = [
        'name',
        'device_brand_id',
        'type',
        'status',
        'created_by',
        'updated_by'
    ];

    public function device_brand()
    {
        return $this->belongsTo(DeviceBrand::class, 'device_brand_id', 'id');
    }

    public function brands(){
        return $this->hasMany(DeviceBrand::class,'device_brand_id','id');
    }
}
