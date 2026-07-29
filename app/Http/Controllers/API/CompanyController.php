<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CompanyController extends BackendBaseController
{

    private $model;
    protected $panel = "Company";
    protected $img_path = "uploads/company/";

    public function __construct()
    {
        $this->model = new Company();
    }
    public function index()
    {
        $companys = $this->model->get();
        $trashed = $this->model->onlyTrashed()->count();
        $trashed_all = $this->model->onlyTrashed()->get();

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' Fetched Successfully',
            'companys' => $companys,
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
                $data['logo'] = $this->uploadImage($request->file('logo'), 'company');
            }

            //1. company Create

            $company = $this->model->create($data + [
                'created_by' => auth('sanctum')->user()->id,
            ]);

            //2. User Create
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('12345'),
                'phone' => $request->phone

            ]);

            // Assign company role
            $user->assignRole('Company');

            // 3. 
            $user->companies()->attach($company->id, [
                'role' => 'Company',
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
        $company = $this->model->findOrFail($id);
        return response()->json([
            'status' => 200,
            'company' => $company
        ]);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $company = $this->model->findOrFail($id);

        $data = $request->except('logo', 'created_by', 'updated_by');

        if ($request->hasFile('logo')) {
            $this->deleteImage($company->logo);
            $data['logo'] = $this->uploadImage($request->file('logo', 'company'));
        }

        $company->update($data + [
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

            $company = Company::findOrFail($id);

            // if ($company->logo) {
            //     $this->deleteImage($company->logo);
            // }

            // Delete all morph relationships
            $company->users()->detach();

            // Delete company
            $company->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => $company->name . ' deleted successfully.'
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

            DB::beginTransaction();

            // $company = Company::findOrFail($id);
            $company = Company::withTrashed()->findOrFail($id);

            if ($company->logo) {
                $this->deleteImage($company->logo);
            }

            // Delete all morph relationships
            $company->users()->detach();

            // Delete company
            $company->forceDelete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => $company->name . ' deleted successfully.'
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
