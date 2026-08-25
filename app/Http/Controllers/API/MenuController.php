<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends BackendBaseController
{

    private $model;
    protected $panel = "Menus ";

    public function __construct()
    {
        $this->model = new Menu();
    }
    public function index()
    {
        $menus = $this->model->with('parent')->orderBy('rank')->get();
        $category = $this->model->with('subCategories')->where('parent_id', null)->orderBy('rank')->get();
        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' Fetched Successfully',
            'menus' => $menus,
            'category' => $category
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
        $request->validate([
            'name' => 'required',
        ]);

        $menu = $this->model->create([
            'name' => $request->name,
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
        $menus = $this->model->with('parent')->whereNull('parent_id')->get();
        return response()->json([
            'status' => 200,
            'menu' => $menu,
            'menus' => $menus
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
            'slug' => Str::slug($request->title),
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
