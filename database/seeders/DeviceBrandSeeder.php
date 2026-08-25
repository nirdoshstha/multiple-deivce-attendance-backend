<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DeviceBrandSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1;

        $brands = [
            [
                'name' => 'ZKTeco',
                'website' => 'https://www.zkteco.com/',
            ],
            [
                'name' => 'Hikvision',
                'website' => 'https://www.hikvision.com/',
            ],
            [
                'name' => 'eSSL',
                'website' => 'https://www.esslsecurity.com/',
            ],
            [
                'name' => 'Anviz',
                'website' => 'https://www.anviz.com/',
            ],
            [
                'name' => 'Suprema',
                'website' => 'https://www.supremainc.com/',
            ],
            [
                'name' => 'Realtime',
                'website' => null,
            ],
            [
                'name' => 'Realand',
                'website' => null,
            ],
        ];

        foreach ($brands as $brand) {
            DB::table('device_brands')->updateOrInsert(
                [
                    'slug' => Str::slug($brand['name']),
                ],
                [
                    'name' => $brand['name'],
                    'slug' => Str::slug($brand['name']),
                    'website' => $brand['website'],
                    'status' => 1,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}