<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        Vendor::create([
            'name' => 'ZKTeco Nepal',
            'email' => 'info@zkteco.com',
            'phone' => '9800000000',
            'address' => 'Kathmandu, Nepal',
            'authorized_person' => 'Ram Shrestha',
            'logo' => null,
            'pan' => '123456789',
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Vendor::create([
            'name' => 'Hikvision Nepal',
            'email' => 'info@hikvision.com',
            'phone' => '9811111111',
            'address' => 'Lalitpur, Nepal',
            'authorized_person' => 'Sita Sharma',
            'logo' => null,
            'pan' => '987654321',
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        Vendor::create([
            'name' => 'Dahua Technology Nepal',
            'email' => 'info@dahua.com',
            'phone' => '9822222222',
            'address' => 'Bhaktapur, Nepal',
            'authorized_person' => 'Hari Prasad',
            'logo' => null,
            'pan' => '456789123',
            'status' => 1,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
    }
}
