<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyDevice extends BackendBaseModel
{
    use SoftDeletes;

    protected $table = 'companies_devices';
    protected $fillable = [
        'name',
        'company_id',
        'device_brand_id',
        'device_id',
        'serial_no',
        'port',
        'api_key',
        'device_code',
        'api_url',
        'ip',
        'status',
        'created_by',
        'updated_by'
    ];


     // 'encrypted' transparently encrypts on save and decrypts on read, so the
    // credential never sits in the database (or in a backup/dump) as plain
    // text. Requires APP_KEY to be set, which Laravel needs anyway.
    protected $casts = [
        'api_key' => 'encrypted',
    ];

    // Never let api_key leak into API responses / debug output by accident.
    protected $hidden = [
        'api_key',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_brand_id', 'id');
    }

    public function brand()
    {
        return $this->belongsTo(DeviceBrand::class, 'device_brand_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function staffLinks()
    {
        return $this->hasMany(DeviceStaffLink::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(DeviceAttendanceLog::class);
    }
}
