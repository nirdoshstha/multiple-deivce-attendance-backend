<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyDevice;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CompanyDeviceController extends BackendBaseController
{

    private $model;
    protected $panel = "Company Devices";

    public function __construct()
    {
        $this->model = new CompanyDevice();
    }
    public function index()
    {
        // $devices = $this->model->get();
        // return response()->json([
        //     'status' => 200,
        //     'message' => $this->panel . ' Fetched Successfully',
        //     'devices' => $devices
        // ]);


        $response = Http::get('https://jsonplaceholder.typicode.com/users');


        if ($response) {
            foreach ($response->json() as $device) {
                CompanyDevice::updateOrCreate(
                    ['ip' => $device['email']],
                    [
                        'name' => $device['name'],
                        'company_id' => '1',
                        'device_brand_id' => '2',
                        'device_id' => '3',
                        'serial_no' => '12345',
                        'port' => '8080',
                        'api_key' => '789',
                        'device_code' => $device['username'],
                        'api_url' => $device['website'],
                        'ip' => $device['email'],
                        'created_by' => auth('sanctum')->user()->id
                    ]
                );
            }
            $devices = $this->model->get();
            $trashed = $this->model->onlyTrashed()->count();
            $trashed_all  = $this->model->onlyTrashed()->get();
            return response()->json([
                'status' => 200,
                'message' => 'Users Imported Successfully',
                'devices' => $devices,
                'trashed' => $trashed,
                'trashed_all' => $trashed_all
            ]);
        }
        return response()->json([
            'status' => 500,
            'message' => 'API Error'
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

        $data = $request->all();

        $device = $this->model->create($data + [
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
        $device = $this->model->with('creator', 'updator')->findOrFail($id);
        return response()->json([
            'status' => 200,
            'device' => $device
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
            'slug' => Str::slug($request->title),
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
        $device = $this->model->findOrFail($id);

        $name = $device->name;

        $device->delete();

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' "' . $name . '" deleted successfully.',
        ]);
    }


    public function restore($id)
    {
        $company = $this->model->onlyTrashed()->findOrFail($id);
        $company->restore();

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' Stored Successfully',
        ]);
    }


    public function destroyPermanent($id)
    {
        try {
            $company = CompanyDevice::withTrashed()->find($id);

            if (!$company) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Company device not found.'
                ], 404);
            }

            $title = $company->title;

            $company->forceDelete();

            return response()->json([
                'status' => 200,
                'message' => $title . ' deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
