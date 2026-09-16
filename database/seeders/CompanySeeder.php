<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Get an existing user for created_by / updated_by
        $userId = User::query()->value('id');

        if (!$userId) {
            $this->command->error('No user found. Please seed users first.');
            return;
        }

        $companies = [
            [
                'name' => 'Allstar Technology Pvt. Ltd.',
                'email' => 'info@allstar.com.np',
                'phone' => '9818319337',
                'address' => 'Kathmandu, Nepal',
                'authorized_person' => 'Kushal Rajbanshi',
                'logo' => null,
                'pan' => '123456789',
                'status' => 0,
            ],
            [
                'name' => 'ABC Trading Pvt. Ltd.',
                'email' => 'info@abctrading.com',
                'phone' => '01-4567891',
                'address' => 'Lalitpur, Nepal',
                'authorized_person' => 'Ram Sharma',
                'logo' => null,
                'pan' => '234567890',
                'status' => 0,
            ],
            [
                'name' => 'XYZ Business Solutions Pvt. Ltd.',
                'email' => 'info@xyzbusiness.com',
                'phone' => '01-4567892',
                'address' => 'Bhaktapur, Nepal',
                'authorized_person' => 'Sita Shrestha',
                'logo' => null,
                'pan' => '345678901',
                'status' => 0,
            ],
        ];

        foreach ($companies as $company) {
            Company::updateOrCreate(
                [
                    'email' => $company['email'],
                ],
                [
                    'name' => $company['name'],
                    'phone' => $company['phone'],
                    'address' => $company['address'],
                    'authorized_person' => $company['authorized_person'],
                    'logo' => $company['logo'],
                    'pan' => $company['pan'],
                    'status' => $company['status'],
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }

        $this->command->info('Companies seeded successfully.');
    }
}
