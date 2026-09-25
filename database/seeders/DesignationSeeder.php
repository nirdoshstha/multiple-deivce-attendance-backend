<?php

namespace Database\Seeders;

use App\Models\Designation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        // Get an existing user for created_by / updated_by
        $userId = User::query()->value('id');

        if (!$userId) {
            $this->command->error('No user found. Please seed users first.');
            return;
        }

        $designations = [
            [
                'name' => 'Chief Executive Officer',
                'key' => 'ceo',
                'status' => true,
            ],
            [
                'name' => 'General Manager',
                'key' => 'general_manager',
                'status' => true,
            ],
            [
                'name' => 'Manager',
                'key' => 'manager',
                'status' => true,
            ],
            [
                'name' => 'Assistant Manager',
                'key' => 'assistant_manager',
                'status' => true,
            ],
            [
                'name' => 'Team Leader',
                'key' => 'team_leader',
                'status' => true,
            ],
             [
                'name' => 'Fullstack Developer',
                'key' => 'fullstack_developer',
                'status' => true,
            ],
            [
                'name' => 'Senior Developer',
                'key' => 'senior_developer',
                'status' => true,
            ],
            [
                'name' => 'Software Developer',
                'key' => 'software_developer',
                'status' => true,
            ],
            [
                'name' => 'Junior Developer',
                'key' => 'junior_developer',
                'status' => true,
            ],
            [
                'name' => 'HR Officer',
                'key' => 'hr_officer',
                'status' => true,
            ],
            [
                'name' => 'Accountant',
                'key' => 'accountant',
                'status' => true,
            ],
            [
                'name' => 'Sales Officer',
                'key' => 'sales_officer',
                'status' => true,
            ],
            [
                'name' => 'Marketing Officer',
                'key' => 'marketing_officer',
                'status' => true,
            ],
            [
                'name' => 'Office Assistant',
                'key' => 'office_assistant',
                'status' => true,
            ],
        ];

        foreach ($designations as $designation) {
            Designation::updateOrCreate(
                [
                    'key' => $designation['key'],
                ],
                [
                    'name' => $designation['name'],
                    'status' => $designation['status'],
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }

        $this->command->info('Designations seeded successfully.');
    }
}
