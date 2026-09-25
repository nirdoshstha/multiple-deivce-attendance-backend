<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Existing user for created_by / updated_by
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

                // Company login
                'password' => '12345678',
            ],
        ];

        foreach ($companies as $companyData) {

            /*
            |--------------------------------------------------------------------------
            | 1. Create / Update Company
            |--------------------------------------------------------------------------
            */
            $company = Company::updateOrCreate(
                [
                    'email' => $companyData['email'],
                ],
                [
                    'name' => $companyData['name'],
                    'phone' => $companyData['phone'],
                    'address' => $companyData['address'],
                    'authorized_person' => $companyData['authorized_person'],
                    'logo' => $companyData['logo'],
                    'pan' => $companyData['pan'],
                    'status' => $companyData['status'],
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | 2. Create / Update User for Company Login
            |--------------------------------------------------------------------------
            */

            $user = User::firstOrCreate(
                [
                    'email' => $companyData['email'],
                ],
                [
                    'name' => $companyData['name'],
                    'phone' => $companyData['phone'],
                    'password' => Hash::make(12345),
                ]
             );

            $user->syncRoles(['Company']);

            /*
            |--------------------------------------------------------------------------
            | 3. Assign Company Role
            |--------------------------------------------------------------------------
            */
            $user->syncRoles(['Company']);

            /*
            |--------------------------------------------------------------------------
            | 4. Connect User with Company
            |--------------------------------------------------------------------------
            */
            $company->users()->syncWithoutDetaching([
                $user->id => [
                    'role' => 'Company',
                ],
            ]);
        }

        $this->command->info('Companies and company users seeded successfully.');
    }
}
