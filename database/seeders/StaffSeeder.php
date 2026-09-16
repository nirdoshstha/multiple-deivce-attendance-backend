<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Designation;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        // Get an existing user for created_by / updated_by
        $createdBy = User::query()->value('id');

        if (!$createdBy) {
            $this->command->error('No user found. Please seed users first.');
            return;
        }

        // Get companies
        $companies = Company::query()
            ->where('status', true)
            ->get();

        if ($companies->isEmpty()) {
            $this->command->error('No companies found. Please run CompanySeeder first.');
            return;
        }

        // Get designations
        $designations = Designation::query()->get();

        if ($designations->isEmpty()) {
            $this->command->error('No designations found. Please run DesignationSeeder first.');
            return;
        }

        $staffs = [
            [
                'name' => 'Ram Sharma',
                'gender' => 'Male',
                'phone' => '9800000001',
                'email' => 'ram.sharma@example.com',
                'address' => 'Kathmandu, Nepal',
                'working_hr' => 8,
                'status' => true,
            ],
            [
                'name' => 'Sita Shrestha',
                'gender' => 'Female',
                'phone' => '9800000002',
                'email' => 'sita.shrestha@example.com',
                'address' => 'Lalitpur, Nepal',
                'working_hr' => 8,
                'status' => true,
            ],
            [
                'name' => 'Hari Thapa',
                'gender' => 'Male',
                'phone' => '9800000003',
                'email' => 'hari.thapa@example.com',
                'address' => 'Bhaktapur, Nepal',
                'working_hr' => 8,
                'status' => true,
            ],
            [
                'name' => 'Gita Gurung',
                'gender' => 'Female',
                'phone' => '9800000004',
                'email' => 'gita.gurung@example.com',
                'address' => 'Kathmandu, Nepal',
                'working_hr' => 8,
                'status' => true,
            ],
            [
                'name' => 'Bikash Karki',
                'gender' => 'Male',
                'phone' => '9800000005',
                'email' => 'bikash.karki@example.com',
                'address' => 'Lalitpur, Nepal',
                'working_hr' => 8,
                'status' => true,
            ],
        ];

        foreach ($staffs as $index => $staff) {

            // Rotate companies/designations for demo data
            $company = $companies[$index % $companies->count()];
            $designation = $designations[$index % $designations->count()];

            // Create a user account for the staff
            $user = User::updateOrCreate(
                [
                    'email' => $staff['email'],
                ],
                [
                    'name' => $staff['name'],
                    'email' => $staff['email'],
                    'phone' => $staff['phone'],
                    'password' => bcrypt('password'),
                ]
            );
            $user->assignRole('Staff');

            Staff::updateOrCreate(
                [
                    'email' => $staff['email'],
                ],
                [
                    'name' => $staff['name'],
                    'company_id' => $company->id,
                    'user_id' => $user->id,
                    'designation_id' => $designation->id,
                    'gender' => $staff['gender'],
                    'phone' => $staff['phone'],
                    'image' => null,
                    'email' => $staff['email'],
                    'address' => $staff['address'],
                    'working_hr' => $staff['working_hr'],
                    'status' => $staff['status'],
                    'created_by' => $createdBy,
                    'updated_by' => $createdBy,
                ]
            );
        }

        $this->command->info('Staff seeded successfully.');
    }
}
