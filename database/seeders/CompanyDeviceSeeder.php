<?php

namespace Database\Seeders;

use App\Models\CompanyDevice;
use Illuminate\Database\Seeder;

class CompanyDeviceSeeder extends Seeder
{
    public function run(): void
    {
        CompanyDevice::create([
            'name' => 'Main Office ZKT K40 Pro',
            'company_id' => 1,
            'device_brand_id' => 1,
            'device_id' => 1,
            'serial_no' => 'ZKTK40PRO001',
            'port' => 4370,
            'api_key' => 123456,
            'device_code' => 'ZKT-001',
            'api_url' => 'http://192.168.1.201/api',
            'ip' => '192.168.1.201',
            'status' => 1,
            'created_by' => 1,
            'updated_by' => null,
        ]);

        CompanyDevice::create([
            'name' => 'Branch Office HK Vision',
            'company_id' => 1,
            'device_brand_id' => 2,
            'device_id' => 2,
            'serial_no' => 'HKV001',
            'port' => 8000,
            'api_key' => 654321,
            'device_code' => 'HKV-001',
            'api_url' => 'http://192.168.1.202/api',
            'ip' => '192.168.1.202',
            'status' => 1,
            'created_by' => 1,
            'updated_by' => null,
        ]);
    }
}