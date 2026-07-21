<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

    public function index()
    {
        $exclude = [
            'sanctum.csrf-cookie',
            'auth.user',
            'user.register',
            'user.login',
        ];
        $routes = collect(Route::getRoutes())
            ->filter(fn($route) => $route->getName())
            ->reject(fn($route) => in_array($route->getName(), $exclude))
            ->map(function ($route) {
                return [
                    'name'   => $route->getName(),
                    'label'  => ucwords(str_replace('.', ' ', $route->getName())),
                    'uri'    => $route->uri(),
                    // 'method' => implode('|', $route->methods()),
                    // 'action' => $route->getActionName(),
                ];
            })
            ->values();

        $permissions = Permission::all()
            ->groupBy(function ($permission) {

                return explode('.', $permission->name)[0];
            });


        return response()->json([
            'status' => 200,
            'routes' => $routes,
            'permissions' => $permissions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
