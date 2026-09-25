<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Use an existing user for created_by / updated_by
        $userId = User::query()->value('id');

        if (!$userId) {
            $this->command->error('No user found. Please seed users first.');
            return;
        }

        $menus = [

            // =========================
            // MAIN MENUS
            // =========================

            [
                'id' => 1,
                'name' => 'Dashboard',
                'permission_id' => null,
                'display_name' => 'Dashboard',
                'slug' => 'dashboard',
                'rank' => 1,
                'icon' => 'bi bi-speedometer2',
                'route' => 'dashboard',
                'parent_id' => null,
                'is_active' => 1,
                'status' => 1,
            ],

            [
                'id' => 2,
                'name' => 'Setting',
                'permission_id' => 84,
                'display_name' => 'Settings',
                'slug' => 'setting',
                'rank' => 12,
                'icon' => 'bi bi-gear',
                'route' => 'setting',
                'parent_id' => null,
                'is_active' => 1,
                'status' => 1,
            ],

            [
                'id' => 3,
                'name' => 'About',
                'permission_id' => 1,
                'display_name' => 'About',
                'slug' => 'about',
                'rank' => 15,
                'icon' => 'bi bi-info-circle',
                'route' => 'about',
                'parent_id' => null,
                'is_active' => 1,
                'status' => 1,
            ],

            [
                'id' => 4,
                'name' => 'Users',
                'permission_id' => null,
                'display_name' => 'Users',
                'slug' => 'users',
                'rank' => 1,
                'icon' => 'bi bi-people',
                'route' => null,
                'parent_id' => null,
                'is_active' => 1,
                'status' => 1,
            ],

            [
                'id' => 5,
                'name' => 'Leave Management',
                'permission_id' => null,
                'display_name' => 'Leave Management',
                'slug' => 'leave-management',
                'rank' => 9,
                'icon' => 'bi bi-calendar-check',
                'route' => null,
                'parent_id' => null,
                'is_active' => 1,
                'status' => 1,
            ],

            [
                'id' => 6,
                'name' => 'Roles',
                'permission_id' => 79,
                'display_name' => 'Roles',
                'slug' => 'roles',
                'rank' => 1,
                'icon' => 'bi bi-person-badge',
                'route' => 'role',
                'parent_id' => null,
                'is_active' => 1,
                'status' => 1,
            ],

            [
                'id' => 7,
                'name' => 'Permissions',
                'permission_id' => 74,
                'display_name' => 'Permissions',
                'slug' => 'permissions',
                'rank' => 7,
                'icon' => 'bi bi-shield-lock',
                'route' => 'permission',
                'parent_id' => null,
                'is_active' => 1,
                'status' => 1,
            ],

            [
                'id' => 8,
                'name' => 'Vendors',
                'permission_id' => 110,
                'display_name' => 'Vendors',
                'slug' => 'vendors',
                'rank' => 2,
                'icon' => 'bi bi-shop',
                'route' => 'vendor',
                'parent_id' => null,
                'is_active' => 1,
                'status' => 1,
            ],

            [
                'id' => 9,
                'name' => 'Devices',
                'permission_id' => null,
                'display_name' => 'Devices Manager',
                'slug' => 'devices',
                'rank' => 11,
                'icon' => 'bi bi-buildings',
                'route' => null,
                'parent_id' => null,
                'is_active' => 1,
                'status' => 1,
            ],

            [
                'id' => 10,
                'name' => 'Staff',
                'permission_id' => null,
                'display_name' => 'Staff Attendance',
                'slug' => 'staff',
                'rank' => 7,
                'icon' => 'bi bi-person-vcard',
                'route' => null,
                'parent_id' => null,
                'is_active' => 1,
                'status' => 1,
            ],

            [
                'id' => 11,
                'name' => 'Menu',
                'permission_id' => 69,
                'display_name' => 'Menu',
                'slug' => 'menu',
                'rank' => 20,
                'icon' => 'bi bi-list',
                'route' => 'menu',
                'parent_id' => null,
                'is_active' => 1,
                'status' => 1,
            ],

            [
                'id' => 12,
                'name' => 'Company',
                'permission_id' => 37,
                'display_name' => 'Company List',
                'slug' => 'company',
                'rank' => 3,
                'icon' => 'bi bi-buildings',
                'route' => 'company',
                'parent_id' => null,
                'is_active' => null,
                'status' => 1,
            ],

            [
                'id' => 13,
                'name' => 'Staff List',
                'permission_id' => 95,
                'display_name' => 'Staff List',
                'slug' => 'staff-list',
                'rank' => 6,
                'icon' => 'bi bi-people',
                'route' => 'staff',
                'parent_id' => null,
                'is_active' => null,
                'status' => 1,
            ],

            // =========================
            // USERS CHILDREN
            // =========================

            [
                'id' => 14,
                'name' => 'Gender',
                'permission_id' => 55,
                'display_name' => 'Gender',
                'slug' => 'gender',
                'rank' => 8,
                'icon' => 'bi bi-gender-ambiguous',
                'route' => 'gender',
                'parent_id' => 4,
                'is_active' => null,
                'status' => 1,
            ],

            [
                'id' => 15,
                'name' => 'Designation',
                'permission_id' => 42,
                'display_name' => 'Designation',
                'slug' => 'designation',
                'rank' => 9,
                'icon' => 'bi bi-person-workspace',
                'route' => 'designation',
                'parent_id' => 4,
                'is_active' => null,
                'status' => 1,
            ],

            [
                'id' => 16,
                'name' => 'User',
                'permission_id' => 102,
                'display_name' => 'User List',
                'slug' => 'user',
                'rank' => 1,
                'icon' => 'bi bi-people',
                'route' => 'user',
                'parent_id' => 4,
                'is_active' => null,
                'status' => 1,
            ],

            // =========================
            // LEAVE MANAGEMENT CHILDREN
            // =========================

            [
                'id' => 17,
                'name' => 'Leave Type',
                'permission_id' => 63,
                'display_name' => 'Leave Type',
                'slug' => 'leave-type',
                'rank' => 5,
                'icon' => 'bi bi-calendar-check',
                'route' => 'leave-type',
                'parent_id' => 5,
                'is_active' => null,
                'status' => 1,
            ],

            [
                'id' => 18,
                'name' => 'Leave Application',
                'permission_id' => 58,
                'display_name' => 'Leave Application',
                'slug' => 'leave-application',
                'rank' => 1,
                'icon' => 'bi bi-calendar-check',
                'route' => 'leave-application',
                'parent_id' => 5,
                'is_active' => null,
                'status' => 1,
            ],

            // =========================
            // DEVICES CHILDREN
            // =========================

            [
                'id' => 19,
                'name' => 'Company',
                'permission_id' => 37,
                'display_name' => 'Company List',
                'slug' => 'company',
                'rank' => 3,
                'icon' => 'bi bi-buildings',
                'route' => 'company',
                'parent_id' => 9,
                'is_active' => null,
                'status' => 1,
            ],

            [
                'id' => 20,
                'name' => 'Company-Devices',
                'permission_id' => 28,
                'display_name' => 'Company Devices',
                'slug' => 'company-devices',
                'rank' => 13,
                'icon' => 'bi bi-pc-display',
                'route' => 'company-device',
                'parent_id' => 9,
                'is_active' => null,
                'status' => 1,
            ],

            [
                'id' => 21,
                'name' => 'Device Brands',
                'permission_id' => 45,
                'display_name' => 'Device Brands',
                'slug' => 'device-brands',
                'rank' => 14,
                'icon' => 'bi bi-tags',
                'route' => 'device-brand',
                'parent_id' => 9,
                'is_active' => null,
                'status' => 1,
            ],

            [
                'id' => 22,
                'name' => 'Devices',
                'permission_id' => 50,
                'display_name' => 'Devices',
                'slug' => 'devices',
                'rank' => 17,
                'icon' => 'bi bi-device-ssd',
                'route' => 'device',
                'parent_id' => 9,
                'is_active' => null,
                'status' => 1,
            ],

            // =========================
            // STAFF CHILDREN
            // =========================

            [
                'id' => 23,
                'name' => 'Staff Device Link',
                'permission_id' => 89,
                'display_name' => 'Staff Device Link',
                'slug' => 'staff-device-link',
                'rank' => 16,
                'icon' => 'bi bi-link-45deg',
                'route' => 'staff-device-link',
                'parent_id' => 10,
                'is_active' => null,
                'status' => 1,
            ],

            [
                'id' => 24,
                'name' => 'Attendance',
                'permission_id' => 17,
                'display_name' => 'Attendance',
                'slug' => 'attendance',
                'rank' => 18,
                'icon' => 'bi bi-calendar2-check',
                'route' => 'attendance',
                'parent_id' => 10,
                'is_active' => null,
                'status' => 1,
            ],

            [
                'id' => 25,
                'name' => 'Attendance Logs',
                'permission_id' => 9,
                'display_name' => 'Attendance Logs',
                'slug' => 'attendance-logs',
                'rank' => 19,
                'icon' => 'bi bi-clock-history',
                'route' => 'attendance-logs',
                'parent_id' => 10,
                'is_active' => null,
                'status' => 1,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['id' => $menu['id']],
                array_merge($menu, [
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ])
            );
        }

        $this->command->info('Menus seeded successfully.');
    }
}
