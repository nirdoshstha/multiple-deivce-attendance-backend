<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Routes to ignore
        $ignoreRoutes = [
            'sanctum.csrf-cookie',
            'auth.user',
            'user.register',
            'user.login',
            'frontend.index',
            'genders.update',
            'genders.show',
            'designations.update',
            'designations.show',
            'storage.local',
            'storage.local.upload',
            'ignition.healthCheck',
            'ignition.executeSolution',
            'ignition.updateConfig',
            'livewire.update',
            'livewire.upload-file',
            'livewire.preview-file',
        ];


        // Get all named routes
        $routes = collect(Route::getRoutes())
            ->filter(function ($route) use ($ignoreRoutes) {

                return $route->getName()
                    && ! in_array($route->getName(), $ignoreRoutes);
            })
            ->sortBy(function ($route) {
                return $route->getName();
            });

        foreach ($routes as $route) {

            Permission::firstOrCreate(
                [
                    'name' => $route->getName(),
                    'guard_name' => 'web',
                ]
            );
        }

        $this->command->info('Permissions generated successfully!');
    }
}
