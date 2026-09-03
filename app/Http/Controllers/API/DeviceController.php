<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceBrand;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;

class DeviceController extends BackendBaseController implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:devices.index', only: ['index']),
            new Middleware('permission:devices.show', only: ['show']),
            new Middleware('permission:devices.store', only: ['store']),
            new Middleware('permission:devices.edit', only: ['edit']),
            new Middleware('permission:devices.update', only: ['update']),
            new Middleware('permission:devices.destroy', only: ['destroy']),
        ];
    }

    private $model;
    protected $panel = "Devices";

    public function __construct()
    {
        $this->model = new Device();
    }
    public function index()
    {
        $devices = Device::with('device_brand')->get();
        $device_brand = DeviceBrand::get();
        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' Fetched Successfully',
            'devices' => $devices,
            'device_brand' => $device_brand
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
            'device_brand_id' => 'required'
        ]);

        $device = $this->model->create([
            'name' => $request->name,
            'device_brand_id' => $request->device_brand_id,
            'type' => $request->type,
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
        $device = $this->model->findOrFail($id);
        $brands = DeviceBrand::get();
        return response()->json([
            'status' => 200,
            'device' => $device,
            'brands' => $brands
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

        $device = $this->model->findOrFail($id);
        $name = $device->name;

        $data = $request->all();

        $device->update([
            'name' => $request->name,
            'device_brand_id' => $request->device_brand_id,
            'type' => $request->type,
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
        $device = $this->model->findOrFail($id);

        $name = $device->name;

        $device->delete();

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' "' . $name . '" deleted successfully.',
        ]);
    }
}
