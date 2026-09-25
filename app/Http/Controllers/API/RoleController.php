<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

// class RoleController extends Controller
class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:roles.index', only: ['index']),
            new Middleware('permission:roles.show', only: ['show']),
            new Middleware('permission:roles.store', only: ['store']),
            new Middleware('permission:roles.edit', only: ['edit']),
            new Middleware('permission:roles.update', only: ['update']),
            new Middleware('permission:roles.destroy', only: ['destroy']),
        ];
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::query()
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($permission) => explode('.', $permission->name)[0]);


        //dd(Route::getRoutes());
        $routes = collect(Route::getRoutes())->map(function ($route) {

            return [
                'uri' => $route->uri(),
                'name' => $route->getName(),
                // ucwords(str_replace('.', ' ', $route->getName()))
                'method' => implode('|', $route->methods()),
                'action' => $route->getActionName(),
            ];
        });


        return response()->json([
            'roles' => $roles,
            'permissions' => $permissions,
            'routes' => $routes
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'permissions' => 'nullable|array'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);
        $role->syncPermissions($request->permissions ?? []);


        $roles = Role::with('permissions')->get();


        return response()->json([
            'status' => 200,
            'message' => 'Role created successfully',
            'roles' => $roles
        ]);
    }

    public function show(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::get()
            ->groupBy(fn($permission) => explode('.', $permission->name)[0]);


        return response()->json([
            'message' => 'Specific Role get successfully',
            'status' => 200,
            'role' => $role,
            'permissions' => $permissions,
        ]);

        // 2nd way
        // $role = Role::find($id);
        // return response()->json([
        //     'message' => 'Specific Role get successfully',
        //     'status' => 200,
        //     'role' => $role,
        //     'permissions' => $role->permissions()->get()
        // ]);

    }

    public function edit(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::get()
            ->groupBy(fn($permission) => explode('.', $permission->name)[0])
            ->map(fn($group) => $group->first())
            ->values();

        return $permissions;
        return response()->json([
            'message' => 'Specific Role get successfully',
            'status' => 200,
            'role' => $role,
        ]);
    }


    public function update(Request $request, string $id)
    {

        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
        ]);

        $role = Role::findOrFail($id);

        $role->update([
            'name' => $request->name,
        ]);


        $role->syncPermissions($request->permissions ?? []);

        return response()->json([
            'status' => 200,
            'message' => 'Role created successfully',
        ]);
    }



public function destroy($id)
{
    try {
        $role = Role::findOrFail($id);

        // Check users assigned to this role
        if ($role->users()->exists()) {
            return response()->json([
                'status' => 422,
                'message' => 'This role cannot be deleted because it is assigned to one or more users.',
            ], 422);
        }

        // Check permissions assigned to this role
        if ($role->permissions()->exists()) {
            return response()->json([
                'status' => 422,
                'message' => 'This role cannot be deleted because it has permissions assigned to it.',
            ], 422);
        }

        $role->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Role deleted successfully',
        ]);

    } catch (\Throwable $e) {

        return response()->json([
            'status' => 500,
            'message' => $e->getMessage(),
        ], 500);
    }
}
}
