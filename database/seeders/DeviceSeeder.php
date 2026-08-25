<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $userId = 1;

        $devices = [

            // ZKTeco
            [
                'brand' => 'ZKTeco',
                'devices' => [
                    ['name' => 'K40', 'type' => 'Fingerprint'],
                    ['name' => 'K40 Pro', 'type' => 'Fingerprint'],
                    ['name' => 'MB20', 'type' => 'Fingerprint + Face'],
                    ['name' => 'MB360', 'type' => 'Fingerprint + Face'],
                    ['name' => 'UA300', 'type' => 'Fingerprint'],
                    ['name' => 'F22', 'type' => 'Fingerprint + Face'],
                    ['name' => 'SpeedFace-V5L', 'type' => 'Face Recognition'],
                    ['name' => 'ProFace X', 'type' => 'Face Recognition'],
                    ['name' => 'G4', 'type' => 'Face Recognition'],
                    ['name' => 'G4L', 'type' => 'Face Recognition'],
                    ['name' => 'Horus E1', 'type' => 'Face Recognition'],
                    ['name' => 'Horus H1', 'type' => 'Face Recognition'],
                    ['name' => 'iClock 260', 'type' => 'Fingerprint'],
                    ['name' => 'iClock 280', 'type' => 'Fingerprint'],
                ],
            ],

            // Hikvision
            [
                'brand' => 'Hikvision',
                'devices' => [
                    ['name' => 'DS-K1T341', 'type' => 'Face Recognition'],
                    ['name' => 'DS-K1T343', 'type' => 'Face Recognition'],
                    ['name' => 'DS-K1T671', 'type' => 'Face Recognition'],
                    ['name' => 'DS-K1T804', 'type' => 'Fingerprint + Face'],
                ],
            ],

            // eSSL
            [
                'brand' => 'eSSL',
                'devices' => [
                    ['name' => 'X990', 'type' => 'Fingerprint'],
                    ['name' => 'X990 Pro', 'type' => 'Fingerprint'],
                    ['name' => 'K30', 'type' => 'Fingerprint'],
                    ['name' => 'X7', 'type' => 'Fingerprint'],
                ],
            ],

            // Anviz
            [
                'brand' => 'Anviz',
                'devices' => [
                    ['name' => 'W2 Pro', 'type' => 'Fingerprint + RFID'],
                    ['name' => 'C2', 'type' => 'Fingerprint + RFID'],
                    ['name' => 'T5 Pro', 'type' => 'Fingerprint'],
                    ['name' => 'FacePass 7', 'type' => 'Face Recognition'],
                ],
            ],

            // Suprema
            [
                'brand' => 'Suprema',
                'devices' => [
                    ['name' => 'BioStation 2', 'type' => 'Fingerprint'],
                    ['name' => 'BioStation 3', 'type' => 'Face Recognition'],
                    ['name' => 'FaceLite', 'type' => 'Face Recognition'],
                ],
            ],

            // Realtime
            [
                'brand' => 'Realtime',
                'devices' => [
                    ['name' => 'Biometric Attendance Terminal', 'type' => 'Fingerprint'],
                    ['name' => 'Face Recognition Terminal', 'type' => 'Face Recognition'],
                ],
            ],

            // Realand
            [
                'brand' => 'Realand',
                'devices' => [
                    ['name' => 'A-C081', 'type' => 'Fingerprint'],
                    ['name' => 'A-C071', 'type' => 'Fingerprint'],
                ],
            ],
        ];

        foreach ($devices as $brandData) {

            $brand = DB::table('device_brands')
                ->where('slug', Str::slug($brandData['brand']))
                ->first();

            if (!$brand) {
                continue;
            }

            foreach ($brandData['devices'] as $device) {

                DB::table('devices')->updateOrInsert(
                    [
                        'device_brand_id' => $brand->id,
                        'name' => $device['name'],
                    ],
                    [
                        'name' => $device['name'],
                        'device_brand_id' => $brand->id,
                        'type' => $device['type'],
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
}