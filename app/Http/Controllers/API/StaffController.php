<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Designation;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class StaffController extends BackendBaseController
{

    private $model;
    protected $panel = "Staffs";
    protected $img_path = 'uploads/staff/';

    public function __construct()
    {
        $this->model = new Staff();
    }
    public function index()
    {


        $trashed = $this->model->onlyTrashed()->count();
        $trashed_all  = $this->model->with('company', 'designation')->onlyTrashed()->get();
        $designations = Designation::get();
        $companies = auth()->user()->companies;


        // Super Admin can see all for this case
        // $user = auth()->user();

        // if ($user->hasRole('Super Admin')) {
        //     $staffs = Staff::with('company', 'designation')->get();
        // } else {
        //     $companies = $user->companies;

        //     $staffs = Staff::with('company', 'designation')
        //         ->whereIn('company_id', $companies->pluck('id'))
        //         ->get();
        // }

        //2nd method
        $user = auth()->user();
        if ($user->can('staffs.view.all')) {
            $staffs = Staff::with('company', 'designation', 'user')->get();
        } else {
            $staffs = Staff::with('company', 'designation', 'user')
                ->whereIn('company_id', $user->companies->pluck('id'))
                ->get();
        }

        return response()->json([
            'status' => 200,
            'message' => 'Users Imported Successfully',
            'staffs' => $staffs,
            'trashed' => $trashed,
            'trashed_all' => $trashed_all,
            'designations' => $designations,
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

        // dd($request->all());
        $request->validate([
            'company_id' => 'required',
            'name' => 'required|string',
            'email' => 'required|email',
            'gender' => 'required|in:male,female,other',
        ]);
        try {
            DB::beginTransaction();

            $data = $request->except('image', 'created_by', 'updated_by');

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->file('image'), 'staff');
            }


            // $data['password'] = Hash::make($request->password);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('12345')
            ]);

            $user->assignRole('Staff');


            $staff = $this->model->create($data + [
                'name'            => $request->name,
                'email'           => $request->email,
                // 'image'           => $data['image'] ?? null,
                'gender'          => $request->gender,
                'phone'           => $request->phone,
                'designation_id'  => $request->designation_id,
                'address'         => $request->address,
                'company_id'      => $request->company_id,
                'user_id'         => $user->id,
                'working_hr'      => $request->working_hr,
                'created_by'      => auth('sanctum')->id(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => $this->panel . ' "' .  $request->name . '" stored successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $staff = $this->model->with('company', 'designation', 'creator', 'updator')->find($id);
        $designations = Designation::get();
        return response()->json([
            'status' => 200,
            'staff' => $staff,
            'designations' => $designations
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
    // public function update(Request $request, string $id)
    // {
    //     $request->validate([
    //         'name'  => 'required|string|max:155',
    //         'email' => 'required|email',
    //     ]);

    //     try {

    //         DB::beginTransaction();

    //         $staff = $this->model->findOrFail($id);

    //         $data = $request->except('image', 'created_by', 'updated_by');

    //         $user = User::findOrFail($staff->user_id);


    //         if ($request->hasFile('image')) {
    //             $this->deleteImage($user->image);
    //             $data['image'] = $this->uploadImage($request->file('image'), 'user');
    //         }


    //         $user->update([
    //             'name'  => $request->name,
    //             'email' => $request->email,
    //             'image'      => $data['image'],
    //         ]);

    //         $staff->update(array_merge($data, [
    //             'name'       => $request->name,
    //             'email'      => $request->email,
    //             'gender'     => $request->gender,
    //             'phone'      => $request->phone,
    //             'address'    => $request->address,
    //             'working_hr' => $request->working_hr,
    //             'updated_by' => auth('sanctum')->id(),
    //         ]));

    //         DB::commit();

    //         return response()->json([
    //             'status'  => 200,
    //             'message' => $this->panel . ' "' . $request->name . '" updated successfully.',
    //         ]);
    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         return response()->json([
    //             'status' => 500,
    //             'message' => $e->getMessage(),
    //         ]);
    //     }
    // }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'  => 'required|string|max:155',
            'email' => 'required|email',
        ]);

        try {
            DB::beginTransaction();

            $staff = $this->model->findOrFail($id);
            $user = User::findOrFail($staff->user_id);

            // Update image only in User model
            if ($request->hasFile('image')) {

                if ($user->image) {
                    $this->deleteImage($user->image);
                }

                $user->image = $this->uploadImage($request->file('image'), 'user');
            }

            // Update user name & email
            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            $user->syncRoles('Staff');

            // Update staff details (exclude image)
            $staff->update([
                'name'         => $request->name,
                'email'        => $request->email,
                'gender'       => $request->gender,
                'phone'        => $request->phone,
                'address'      => $request->address,
                'working_hr'   => $request->working_hr,
                'company_id'   => $request->company_id,
                'designation_id' => $request->designation_id,
                'updated_by'   => auth('sanctum')->id(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => $this->panel . ' "' . $request->name . '" updated successfully.',
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $staff = $this->model->findOrFail($id);

        $name = $staff->name;

        $staff->delete();

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' "' . $name . '" deleted successfully.',
        ]);
    }


    public function restore($id)
    {
        $staff = $this->model->with('company')->onlyTrashed()->findOrFail($id);
        $staff->restore();

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' Stored Successfully',
        ]);
    }


    public function destroyPermanent($id)
    {
        try {
            $staff = $this->model->withTrashed()->find($id);

            if (!$staff) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Company staff not found.'
                ], 404);
            }

            $title = $staff->title;

            // $user_id = $staff->user_id;
            // $user = User::where('id', $user_id)->first();

            $staff->forceDelete();
            // $user->delete();


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

    public function searchStaff(Request $request)
    {
        $search = $request->input('search');
        $staffs = $this->model->with('company', 'designation')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        $designations = Designation::get();
        $companies = auth()->user()->companies;

        return response()->json([
            'staffs' => $staffs,
            'designations' => $designations,
            'companies' => $companies
        ]);
    }
}
