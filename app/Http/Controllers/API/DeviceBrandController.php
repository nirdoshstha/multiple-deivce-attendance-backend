<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DeviceBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceBrandController extends BackendBaseController
{

    private $model;
    protected $panel = "Device Brand";

    public function __construct()
    {
        $this->model = new DeviceBrand();
    }
    public function index()
    {
        $brands = $this->model->get();
        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' Fetched Successfully',
            'brands' => $brands
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

        $brand = $this->model->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'website' => $request->website,
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
        $brand = $this->model->findOrFail($id);
        return response()->json([
            'status' => 200,
            'brand' => $brand
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

        $brand = $this->model->findOrFail($id);
        $name = $brand->name;

        $data = $request->all();

        $brand->update([
            'name' => $request->name,
            'slug'=> Str::slug($request->title),
            'website' => $request->website,
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
        $brand = $this->model->findOrFail($id);

        $name = $brand->name;

        $brand->delete();

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' "' . $name . '" deleted successfully.',
        ]);
    }
}
