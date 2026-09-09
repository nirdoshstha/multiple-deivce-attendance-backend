<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [

            // Dashboard
            [
                'name'         => 'dashboard',
                'display_name' => 'Dashboard',
                'slug'         => 'dashboard',
                'rank'         => 1,
                'icon'         => 'bi bi-speedometer2',
                'route'        => 'dashboard',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Settings
            [
                'name'         => 'setting',
                'display_name' => 'Settings',
                'slug'         => 'setting',
                'rank'         => 2,
                'icon'         => 'bi bi-gear',
                'route'        => 'setting',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // About
            [
                'name'         => 'about',
                'display_name' => 'About',
                'slug'         => 'about',
                'rank'         => 3,
                'icon'         => 'bi bi-info-circle',
                'route'        => 'about',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Users
            [
                'name'         => 'users',
                'display_name' => 'Users',
                'slug'         => 'user',
                'rank'         => 4,
                'icon'         => 'bi bi-people',
                'route'        => 'user',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Leave Management
            [
                'name'         => 'leave-management',
                'display_name' => 'Leave Management',
                'slug'         => 'leave-management',
                'rank'         => 5,
                'icon'         => 'bi bi-calendar-check',
                'route'        => 'leave-application',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Roles
            [
                'name'         => 'roles',
                'display_name' => 'Roles',
                'slug'         => 'role',
                'rank'         => 6,
                'icon'         => 'bi bi-person-badge',
                'route'        => 'role',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Permissions
            [
                'name'         => 'permissions',
                'display_name' => 'Permissions',
                'slug'         => 'permission',
                'rank'         => 7,
                'icon'         => 'bi bi-shield-lock',
                'route'        => 'permission',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Gender
            [
                'name'         => 'gender',
                'display_name' => 'Gender',
                'slug'         => 'gender',
                'rank'         => 8,
                'icon'         => 'bi bi-gender-ambiguous',
                'route'        => 'gender',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Designation
            [
                'name'         => 'designation',
                'display_name' => 'Designation',
                'slug'         => 'designation',
                'rank'         => 9,
                'icon'         => 'bi bi-person-workspace',
                'route'        => 'designation',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Vendors
            [
                'name'         => 'vendors',
                'display_name' => 'Vendors',
                'slug'         => 'vendor',
                'rank'         => 10,
                'icon'         => 'bi bi-shop',
                'route'        => 'vendor',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Companies
            [
                'name'         => 'companies',
                'display_name' => 'Companies',
                'slug'         => 'company',
                'rank'         => 11,
                'icon'         => 'bi bi-buildings',
                'route'        => 'company',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Device Manager
            [
                'name'         => 'device-manager',
                'display_name' => 'Device Manager',
                'slug'         => 'device-manager',
                'rank'         => 12,
                'icon'         => 'bi bi-hdd-stack',
                'route'        => 'device-manager',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Company Devices
            [
                'name'         => 'company-devices',
                'display_name' => 'Company Devices',
                'slug'         => 'company-device',
                'rank'         => 13,
                'icon'         => 'bi bi-pc-display',
                'route'        => 'company-device',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Device Brands
            [
                'name'         => 'device-brands',
                'display_name' => 'Device Brands',
                'slug'         => 'device-brand',
                'rank'         => 14,
                'icon'         => 'bi bi-tags',
                'route'        => 'device-brand',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Staffs
            [
                'name'         => 'staffs',
                'display_name' => 'Staffs',
                'slug'         => 'staff',
                'rank'         => 15,
                'icon'         => 'bi bi-person-vcard',
                'route'        => 'staff',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Staff Device Link
            [
                'name'         => 'staff-device-link',
                'display_name' => 'Staff Device Links',
                'slug'         => 'staff-device-link',
                'rank'         => 16,
                'icon'         => 'bi bi-link-45deg',
                'route'        => 'staff-device-link',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Devices
            [
                'name'         => 'devices',
                'display_name' => 'Devices',
                'slug'         => 'device',
                'rank'         => 17,
                'icon'         => 'bi bi-device-ssd',
                'route'        => 'device',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Attendance
            [
                'name'         => 'attendance',
                'display_name' => 'Attendance',
                'slug'         => 'attendance',
                'rank'         => 18,
                'icon'         => 'bi bi-calendar2-check',
                'route'        => 'attendance',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],

            // Attendance Logs
            [
                'name'         => 'attendance-logs',
                'display_name' => 'Attendance Logs',
                'slug'         => 'attendance-logs',
                'rank'         => 19,
                'icon'         => 'bi bi-clock-history',
                'route'        => 'attendance-logs',
                'parent_id'    => null,
                'permission_id' => null,
                'is_active'    => true,
                'status'       => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['slug' => $menu['slug']],
                $menu
            );
        }
    }
}
