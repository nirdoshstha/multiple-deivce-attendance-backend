<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create the Super Admin role
        $role = Role::firstOrCreate([
            'name' => 'Super Admin',
        ]);

        // Create the Super Admin user
        $user = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Nirdosh Shrestha',
                'password' => Hash::make('12345'),
            ]
        );

        // Assign the role
        $user->syncRoles([$role]);
    }
}