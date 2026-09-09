<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyDevice;
use App\Models\Device;
use App\Models\DeviceBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use App\Services\FingerprintDeviceService;
use Illuminate\Http\JsonResponse;
use Throwable;

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
        $user = auth()->user();

        // $devices = $this->model->with('brand', 'company')->get();
        $device_brand = DeviceBrand::get();
        $device_name = Device::get();
        $companies = $user->companies;



        // Super Admin can see all for this case

        if ($user->hasRole('Super Admin')) {

            $devices = $this->model
                ->with(['device', 'brand', 'company'])
                ->get();
        } else {

            $devices = CompanyDevice::with(['device', 'brand', 'company'])
                ->whereIn('company_id', $user->companies()->pluck('companies.id'))
                ->get();
        }
        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' Fetched Successfully',
            'devices' => $devices,
            'device_name' => $device_name,
            'device_brand' => $device_brand,
            'companies' => $companies
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
            'company_id' => 'required',
            'device_id' => 'required',
            'serial_no' => 'required'
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

    public function getDevicesByBrand(Request $request)
    {
        $devices = Device::where('device_brand_id', $request->device_brand_id)
            ->get(['id', 'name', 'device_brand_id']);

        return response()->json([
            'devices' => $devices
        ]);
    }



    // POST /api/company-devices/{companyDevice}/check-connection
    // A lightweight ping: connect + verify serial number, no data pulled.
    public function checkConnection(CompanyDevice $companyDevice): JsonResponse
    {

        // Push devices never accept an inbound connection — they call you.
        // "Checking connection" for these means "did it check in recently,"
        // not opening a socket (which would just time out and mislabel a
        // perfectly healthy device as offline).
        if ($companyDevice->connection_mode === 'push') {
            $isOnline = $companyDevice->last_seen_at
                && $companyDevice->last_seen_at->gt(now()->subMinutes(5));

            return response()->json([
                'status' => $isOnline ? 'online' : 'offline',
                'serial_no' => $companyDevice->serial_no,
                'last_seen_at' => $companyDevice->last_seen_at,
            ], $isOnline ? 200 : 422);
        }


        $service = new FingerprintDeviceService($companyDevice);


        try {
            $service->connect();
            $service->disconnect();

            return response()->json([
                'status' => 'online',
                'serial_no' => $companyDevice->serial_no,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'status' => 'offline',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    // POST /api/company-devices/{companyDevice}/sync
    public function sync(CompanyDevice $companyDevice): JsonResponse
    {

        // Push devices send data on their own schedule — there's nothing to
        // "pull." Make that explicit instead of trying (and failing) to
        // connect outbound to a device that only calls inbound.
        if ($companyDevice->connection_mode === 'push') {
            return response()->json([
                'message' => 'This device pushes attendance automatically — manual sync isn\'t applicable. Check last_seen_at to confirm it\'s checking in.',
            ], 422);
        }
        
        try {
            $summary = (new FingerprintDeviceService($companyDevice))->sync();

            return response()->json(['data' => $summary]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 422);
        }
    }
}
