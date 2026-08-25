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
}
