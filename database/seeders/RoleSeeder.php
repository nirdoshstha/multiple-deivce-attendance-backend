<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'guard_name' => 'web',
            ],
            [
                'name' => 'Vendor',
                'guard_name' => 'web',
            ],
            [
                'name' => 'Company',
                'guard_name' => 'web',
            ],
            [
                'name' => 'Staff',
                'guard_name' => 'web',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                [
                    'name' => $role['name'],
                    'guard_name' => $role['guard_name'],
                ],
                $role
            );
        }

        $this->command->info('Roles seeded successfully.');
    }
}
