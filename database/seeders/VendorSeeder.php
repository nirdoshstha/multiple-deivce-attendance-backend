<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        // Existing user for created_by / updated_by
        $userId = User::query()->value('id');

        if (!$userId) {
            $this->command->error(
                'No user found. Please seed users first.'
            );

            return;
        }

        $vendors = [
            // [
            //     'name' => 'ZKTeco Nepal',
            //     'email' => 'info@zkteco.com',
            //     'phone' => '9800000000',
            //     'address' => 'Kathmandu, Nepal',
            //     'authorized_person' => 'Ram Shrestha',
            //     'logo' => null,
            //     'pan' => '123456789',
            //     'status' => 1,
            //     'password' => '12345678',
            // ],

            // [
            //     'name' => 'Hikvision Nepal',
            //     'email' => 'info@hikvision.com',
            //     'phone' => '9811111111',
            //     'address' => 'Lalitpur, Nepal',
            //     'authorized_person' => 'Sita Sharma',
            //     'logo' => null,
            //     'pan' => '987654321',
            //     'status' => 1,
            //     'password' => '12345678',
            // ],

            // [
            //     'name' => 'Dahua Technology Nepal',
            //     'email' => 'info@dahua.com',
            //     'phone' => '9822222222',
            //     'address' => 'Bhaktapur, Nepal',
            //     'authorized_person' => 'Hari Prasad',
            //     'logo' => null,
            //     'pan' => '456789123',
            //     'status' => 1,
            //     'password' => '12345678',
            // ],

            [
                'name' => 'Vendor One',
                'email' => 'vendor1@gmail.com',
                'phone' => '+1 (177) 393-7351',
                'address' => 'Alias nostrum volupt',
                'authorized_person' => null,
                'logo' => null,
                'pan' => 'Qui enim est do moll',
                'status' => 0,
                'password' => '12345678',
            ],
        ];

        foreach ($vendors as $vendorData) {

            /*
            |--------------------------------------------------------------------------
            | 1. Create / Update Vendor
            |--------------------------------------------------------------------------
            */
            $vendor = Vendor::updateOrCreate(
                [
                    'email' => $vendorData['email'],
                ],
                [
                    'name' => $vendorData['name'],
                    'phone' => $vendorData['phone'],
                    'address' => $vendorData['address'],
                    'authorized_person' =>
                        $vendorData['authorized_person'],
                    'logo' => $vendorData['logo'],
                    'pan' => $vendorData['pan'],
                    'status' => $vendorData['status'],
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 2. Create / Update User
            |--------------------------------------------------------------------------
            |
            | Vendor uses the same email for login.
            |
            */
            $user = User::firstOrCreate(
                [
                    'email' => $vendorData['email'],
                ],
                [
                    'name' => $vendorData['name'],
                    'phone' => $vendorData['phone'],
                    'password' => Hash::make(12345),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 3. Assign Vendor Role
            |--------------------------------------------------------------------------
            */
            $user->syncRoles(['Vendor']);

            /*
            |--------------------------------------------------------------------------
            | 4. Connect User with Vendor
            |--------------------------------------------------------------------------
            */
            $vendor->users()->syncWithoutDetaching([
                $user->id => [
                    'role' => 'Vendor',
                ],
            ]);
        }

        $this->command->info(
            'Vendors and vendor users seeded successfully.'
        );
    }
}
