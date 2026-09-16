<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Get an existing user for created_by / updated_by
        $userId = User::query()->value('id');

        if (!$userId) {
            $this->command->error('No user found. Please seed users first.');
            return;
        }

        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'days_per_year' => 18,
                'is_paid' => true,
                'requires_approval' => true,
                'allow_half_day' => true,
                'status' => true,
            ],
            [
                'name' => 'Sick Leave',
                'days_per_year' => 12,
                'is_paid' => true,
                'requires_approval' => true,
                'allow_half_day' => true,
                'status' => true,
            ],
            [
                'name' => 'Casual Leave',
                'days_per_year' => 10,
                'is_paid' => true,
                'requires_approval' => true,
                'allow_half_day' => true,
                'status' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'days_per_year' => 98,
                'is_paid' => true,
                'requires_approval' => true,
                'allow_half_day' => false,
                'status' => true,
            ],
            [
                'name' => 'Paternity Leave',
                'days_per_year' => 15,
                'is_paid' => true,
                'requires_approval' => true,
                'allow_half_day' => false,
                'status' => true,
            ],
            [
                'name' => 'Unpaid Leave',
                'days_per_year' => 0,
                'is_paid' => false,
                'requires_approval' => true,
                'allow_half_day' => true,
                'status' => true,
            ],
            [
                'name' => 'Bereavement Leave',
                'days_per_year' => 5,
                'is_paid' => true,
                'requires_approval' => true,
                'allow_half_day' => false,
                'status' => true,
            ],
            [
                'name' => 'Study Leave',
                'days_per_year' => 10,
                'is_paid' => false,
                'requires_approval' => true,
                'allow_half_day' => true,
                'status' => true,
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::updateOrCreate(
                [
                    'slug' => Str::slug($leaveType['name']),
                ],
                [
                    'name' => $leaveType['name'],
                    'days_per_year' => $leaveType['days_per_year'],
                    'is_paid' => $leaveType['is_paid'],
                    'requires_approval' => $leaveType['requires_approval'],
                    'allow_half_day' => $leaveType['allow_half_day'],
                    'status' => $leaveType['status'],
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );
        }

        $this->command->info('Leave types seeded successfully.');
    }
}
