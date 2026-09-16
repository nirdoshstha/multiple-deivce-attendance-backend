<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Created By
        |--------------------------------------------------------------------------
        */
        $user = User::where('email', 'superadmin@gmail.com')->first();

        $createdBy = $user?->id ?? User::query()->value('id');

        if (!$createdBy) {
            $this->command->error('No user found.');
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Parent Menus
        |--------------------------------------------------------------------------
        */
        $parents = [
            [
                'name' => 'Dashboard',
                'display_name' => 'Dashboard',
                'slug' => 'dashboard',
                'rank' => 1,
                'icon' => 'bi bi-speedometer2',
                'route' => 'dashboard',
                'is_active' => 1,
            ],

            [
                'name' => 'Setting',
                'display_name' => 'Settings',
                'slug' => 'setting',
                'rank' => 12,
                'icon' => 'bi bi-gear',
                'route' => 'setting',
                'is_active' => 1,
            ],

            [
                'name' => 'About',
                'display_name' => 'About',
                'slug' => 'about',
                'rank' => 15,
                'icon' => 'bi bi-info-circle',
                'route' => 'about',
                'is_active' => 1,
            ],

            [
                'name' => 'Users',
                'display_name' => 'Users',
                'slug' => 'users',
                'rank' => 1,
                'icon' => 'bi bi-people',
                'route' => null,
                'is_active' => 1,
            ],

            [
                'name' => 'Leave Management',
                'display_name' => 'Leave Management',
                'slug' => 'leave-management',
                'rank' => 9,
                'icon' => 'bi bi-calendar-check',
                'route' => 'leave-management',
                'is_active' => 1,
            ],

            [
                'name' => 'Roles',
                'display_name' => 'Roles',
                'slug' => 'roles',
                'rank' => 1,
                'icon' => 'bi bi-person-badge',
                'route' => 'role',
                'is_active' => 1,
            ],

            [
                'name' => 'Permissions',
                'display_name' => 'Permissions',
                'slug' => 'permissions',
                'rank' => 7,
                'icon' => 'bi bi-shield-lock',
                'route' => 'permission',
                'is_active' => 1,
            ],

            [
                'name' => 'Vendors',
                'display_name' => 'Vendors',
                'slug' => 'vendors',
                'rank' => 2,
                'icon' => 'bi bi-shop',
                'route' => 'vendor',
                'is_active' => 1,
            ],

            [
                'name' => 'Devices',
                'display_name' => 'Devices Manager',
                'slug' => 'devices',
                'rank' => 11,
                'icon' => 'bi bi-buildings',
                'route' => null,
                'is_active' => 1,
            ],

            [
                'name' => 'Staff',
                'display_name' => 'Staff Attendance',
                'slug' => 'staff',
                'rank' => 7,
                'icon' => 'bi bi-person-vcard',
                'route' => null,
                'is_active' => 1,
            ],

            [
                'name' => 'Menu',
                'display_name' => 'Menu',
                'slug' => 'menu',
                'rank' => 20,
                'icon' => 'bi bi-list',
                'route' => 'menu',
                'is_active' => 1,
            ],

            /*
            |--------------------------------------------------------------------------
            | Company
            |--------------------------------------------------------------------------
            |
            | This is also a top-level menu in your SQL.
            |
            */
            [
                'name' => 'Company',
                'display_name' => 'Company List',
                'slug' => 'company',
                'rank' => 3,
                'icon' => 'bi bi-buildings',
                'route' => 'company',
                'is_active' => null,
            ],

            /*
            |--------------------------------------------------------------------------
            | Staff List
            |--------------------------------------------------------------------------
            |
            | This is also a top-level menu in your SQL.
            |
            */
            [
                'name' => 'Staff List',
                'display_name' => 'Staff List',
                'slug' => 'staff-list',
                'rank' => 6,
                'icon' => 'bi bi-people',
                'route' => 'staff',
                'is_active' => null,
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Insert / Update Parent Menus
        |--------------------------------------------------------------------------
        */
        foreach ($parents as $menu) {
            Menu::updateOrCreate(
                [
                    'slug' => $menu['slug'],
                    'parent_id' => null,
                ],
                [
                    'name' => $menu['name'],
                    'permission_id' => null,
                    'display_name' => $menu['display_name'],
                    'rank' => $menu['rank'],
                    'icon' => $menu['icon'],
                    'route' => $menu['route'],
                    'parent_id' => null,
                    'is_active' => $menu['is_active'],
                    'status' => 1,
                    'created_by' => $createdBy,
                    'updated_by' => null,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Get Parent IDs
        |--------------------------------------------------------------------------
        */
        $parentIds = Menu::whereNull('parent_id')
            ->whereIn('slug', [
                'users',
                'leave-management',
                'devices',
                'staff',
            ])
            ->pluck('id', 'slug');

        /*
        |--------------------------------------------------------------------------
        | Children
        |--------------------------------------------------------------------------
        */
        $children = [
            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Gender',
                'display_name' => 'Gender',
                'slug' => 'gender',
                'rank' => 8,
                'icon' => 'bi bi-gender-ambiguous',
                'route' => 'gender',
                'parent' => 'users',
            ],

            [
                'name' => 'Designation',
                'display_name' => 'Designation',
                'slug' => 'designation',
                'rank' => 9,
                'icon' => 'bi bi-person-workspace',
                'route' => 'designation',
                'parent' => 'users',
            ],

            [
                'name' => 'User',
                'display_name' => 'User List',
                'slug' => 'user',
                'rank' => 1,
                'icon' => 'bi bi-people',
                'route' => 'user',
                'parent' => 'users',
            ],

            /*
            |--------------------------------------------------------------------------
            | Leave Management
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Leave Type',
                'display_name' => 'Leave Type',
                'slug' => 'leave-type',
                'rank' => 5,
                'icon' => 'bi bi-calendar-check',
                'route' => 'leave-type',
                'parent' => 'leave-management',
            ],

            [
                'name' => 'Leave Application',
                'display_name' => 'Leave Application',
                'slug' => 'leave-application',
                'rank' => 1,
                'icon' => 'bi bi-calendar-check',
                'route' => 'leave-application',
                'parent' => 'leave-management',
            ],

            /*
            |--------------------------------------------------------------------------
            | Devices
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Company',
                'display_name' => 'Company List',
                'slug' => 'company',
                'rank' => 3,
                'icon' => 'bi bi-buildings',
                'route' => 'company',
                'parent' => 'devices',
            ],

            [
                'name' => 'Company-Devices',
                'display_name' => 'Company Devices',
                'slug' => 'company-device',
                'rank' => 13,
                'icon' => 'bi bi-pc-display',
                'route' => 'company-device',
                'parent' => 'devices',
            ],

            [
                'name' => 'Device Brands',
                'display_name' => 'Device Brands',
                'slug' => 'device-brand',
                'rank' => 14,
                'icon' => 'bi bi-tags',
                'route' => 'device-brand',
                'parent' => 'devices',
            ],

            [
                'name' => 'Devices',
                'display_name' => 'Devices',
                'slug' => 'device',
                'rank' => 17,
                'icon' => 'bi bi-device-ssd',
                'route' => 'device',
                'parent' => 'devices',
            ],

            /*
            |--------------------------------------------------------------------------
            | Staff
            |--------------------------------------------------------------------------
            */
            [
                'name' => 'Staff Device Link',
                'display_name' => 'Staff Device Link',
                'slug' => 'staff-device-link',
                'rank' => 16,
                'icon' => 'bi bi-link-45deg',
                'route' => 'staff-device-link',
                'parent' => 'staff',
            ],

            [
                'name' => 'Attendance',
                'display_name' => 'Attendance',
                'slug' => 'attendance',
                'rank' => 18,
                'icon' => 'bi bi-calendar2-check',
                'route' => 'attendance',
                'parent' => 'staff',
            ],

            [
                'name' => 'Attendance Logs',
                'display_name' => 'Attendance Logs',
                'slug' => 'attendance-logs',
                'rank' => 19,
                'icon' => 'bi bi-clock-history',
                'route' => 'attendance-logs',
                'parent' => 'staff',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Insert / Update Children
        |--------------------------------------------------------------------------
        */
        foreach ($children as $menu) {

            $parentId = $parentIds[$menu['parent']] ?? null;

            if (!$parentId) {
                $this->command->warn(
                    "Parent menu not found: {$menu['parent']}"
                );

                continue;
            }

            Menu::updateOrCreate(
                [
                    'slug' => $menu['slug'],
                    'parent_id' => $parentId,
                ],
                [
                    'name' => $menu['name'],
                    'permission_id' => null,
                    'display_name' => $menu['display_name'],
                    'rank' => $menu['rank'],
                    'icon' => $menu['icon'],
                    'route' => $menu['route'],
                    'parent_id' => $parentId,
                    'is_active' => null,
                    'status' => 1,
                    'created_by' => $createdBy,
                    'updated_by' => null,
                ]
            );
        }

        $this->command->info(
            'Menu seeder completed successfully.'
        );
    }
}
