<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VendorController extends BackendBaseController
{

    private $model;
    protected $panel = "Vendor";
    protected $img_path = "uploads/vendor/";

    public function __construct()
    {
        $this->model = new Vendor();
    }
    public function index()
    {
        $vendors = $this->model->get();
        $trashed = $this->model->onlyTrashed()->count();
        $trashed_all = $this->model->onlyTrashed()->get();
        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' Fetched Successfully',
            'vendors' => $vendors,
            'trashed' => $trashed,
            'trashed_all' => $trashed_all
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
            'email' => 'required|email'
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except('logo', 'created_by', 'updated_by');

            if ($request->hasFile('logo')) {
                $data['logo'] = $this->uploadImage($request->file('logo'), 'vendor');
            }

            //1. Vendor Create

            $vendor = $this->model->create($data + [
                'created_by' => auth('sanctum')->user()->id,
            ]);

            //2. User Create
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('12345'),
                'phone' => $request->phone

            ]);

            // Assign Vendor role
            $user->assignRole('Vendor');

            // 3. 
            $user->vendors()->attach($vendor->id, [
                'role' => 'Vendor',
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => $this->panel . ' stored successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }


    public function show(string $id)
    {
        $vendor = $this->model->findOrFail($id);
        return response()->json([
            'status' => 200,
            'vendor' => $vendor
        ]);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $vendor = $this->model->findOrFail($id);

        $data = $request->except('logo', 'created_by', 'updated_by');

        if ($request->hasFile('logo')) {
            $this->deleteImage($vendor->logo);
            $data['logo'] = $this->uploadImage($request->file('logo', 'vendor'));
        }

        $vendor->update($data + [
            'updated_by' => auth('sanctum')->user()->id,
        ]);

        return response()->json([
            'status' => 201,
            'message' => $this->panel . ' updated successfully',
        ]);
    }

    public function destroy($id)
    {
        try {

            DB::beginTransaction();

            $vendor = Vendor::findOrFail($id);

            // if ($vendor->logo) {
            //     $this->deleteImage($vendor->logo);
            // }

            // Delete all morph relationships
            $vendor->users()->detach();

            // Delete vendor
            $vendor->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => $vendor->name . ' deleted successfully.'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function restore($id)
    {
        $vendor = $this->model->onlyTrashed()->findOrFail($id);
        $vendor->restore();

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' Stored Successfully',
        ]);
    }


    public function destroyPermanent($id)
    {
        try {

            DB::beginTransaction();

            // $vendor = Vendor::findOrFail($id);
            $vendor = Vendor::withTrashed()->findOrFail($id);

            if ($vendor->logo) {
                $this->deleteImage($vendor->logo);
            }

            // Delete all morph relationships
            $vendor->users()->detach();

            // Delete vendor
            $vendor->forceDelete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => $vendor->name . ' deleted successfully.'
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
