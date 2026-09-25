<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class MenuController extends BackendBaseController implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:menus.index', only: ['index']),
            new Middleware('permission:menus.show', only: ['show']),
            new Middleware('permission:menus.store', only: ['store']),
            new Middleware('permission:menus.edit', only: ['edit']),
            new Middleware('permission:menus.update', only: ['update']),
            new Middleware('permission:menus.destroy', only: ['destroy']),
        ];
    }

    private $model;
    protected $panel = "Menus ";

    public function __construct()
    {
        $this->model = new Menu();
    }
    // public function index()
    // {
    //     $menus = $this->model->with('parent', 'subCategories')->orderBy('rank')->get();
    //     // $category = $this->model->with('subCategories')->where('parent_id', null)->orderBy('rank')->get();
    //     $category = Menu::with([
    //         'permission',
    //         'subCategories',
    //     ])
    //         ->whereNull('parent_id')
    //         ->where('status', 1)
    //         ->orderBy('rank')
    //         ->get();

    //     return response()->json([
    //         'status' => 200,
    //         'message' => $this->panel . ' Fetched Successfully',
    //         'menus' => $menus,
    //         'category' => $category,
    //     ]);
    // }

    public function index()
    {
        $user = auth('sanctum')->user();

        $menus = $this->model->with('parent', 'subCategories')->orderBy('rank')->get();

        $permissions = Permission::where('name', "like", "%.index")->get(); //get data only eg:about.index, menu.index

        $category = Menu::with(['permission', 'subCategories'])
            ->whereNull('parent_id')
            ->where('status', 1)
            ->orderBy('rank')
            ->get();

        $category = $this->filterMenusByPermission($category, $user);

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' Fetched Successfully',
            'menus' => $menus,
            'category' => $category,
            'permissions' => $permissions
        ]);
    }

    /**
     * Recursively drop any menu (and its children) the user isn't allowed to see.
     * A menu with no permission_id is treated as public.
     */
    private function filterMenusByPermission($menus, $user)
    {
        return $menus
            ->filter(function ($menu) use ($user) {
                return !$menu->permission || $user->can($menu->permission->name);
            })
            ->map(function ($menu) use ($user) {
                if ($menu->subCategories && $menu->subCategories->count()) {
                    $menu->setRelation(
                        'subCategories',
                        $this->filterMenusByPermission($menu->subCategories, $user)
                    );
                }
                return $menu;
            })
            ->values();
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
        $request->validate([
            'name' => 'required',
        ]);

        $menu = $this->model->create([
            'name' => $request->name,
            'permission_id' => $request->permission_id,
            'parent_id' => $request->parent_id,
            'display_name' => $request->display_name,
            'slug' => Str::slug($request->name),
            'rank' => $request->rank,
            'icon' => $request->icon,
            'route' => $request->route,
            'created_by' => auth('sanctum')->user()->id,
        ]);



        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' "' .  $request->name . '" stored successfully.',
        ]);
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $menu = $this->model->findOrFail($id);
        // $menus = $this->model->with('parent')->whereNull('parent_id')->get();

        $permissions = Permission::where('name', "like", "%.index")->get();
        $parents = $this->model->has('subCategories')->get();
        return response()->json([
            'status' => 200,
            'menu' => $menu,
            'parents' => $parents,
            'permissions' => $permissions
        ]);
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
        $request->validate([
            'name' => 'required|string|max:155'
        ]);

        $menu = $this->model->findOrFail($id);
        $name = $menu->name;

        $data = $request->all();

        $menu->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'slug' => Str::slug($request->name),
            'permission_id' => $request->permission_id,
            'route' => $request->route,
            'rank' => $request->rank,
            'icon' => $request->icon,
            'parent_id' => $request->parent_id,
            'updated_by' => auth('sanctum')->user()->id,
        ]);

        return response()->json([
            'status' => 201,
            'message' => $this->panel . ' "' . $name . '" updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $menu = $this->model->findOrFail($id);

        $name = $menu->name;

        $menu->delete();

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' "' . $name . '" deleted successfully.',
        ]);
    }
}
