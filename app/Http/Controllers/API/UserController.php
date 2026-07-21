<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

// class UserController extends BackendBaseController
class UserController extends BackendBaseController implements HasMiddleware

{
    protected $model;
    protected $panel = 'User';
    protected $img_path = 'uploads/user/';

    public function __construct()
    {
        $this->model = new User();
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permission:users.index', only: ['index']),
            new Middleware('permission:users.show', only: ['show']),
            new Middleware('permission:users.store', only: ['store']),
            new Middleware('permission:users.update', only: ['update']),
            new Middleware('permission:users.destroy', only: ['destroy']),
        ];
    }

    public function index()
    {

        $users = User::with('roles')->get();
        return response()->json([
            'users' => $users,
            'roles' => Role::get()
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
            'email' => 'required|unique:users,email',
            'password' => 'required|min:5|max:15',
            'roles' => 'required',
        ]);

        $data = $request->except('image');
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), 'user');
        }

        $data['password'] = Hash::make($request->password);
        $user = User::create($data);
        $user->syncRoles($request->roles);

        return response()->json([
            'status' => 200,
            'message' => 'User & Role Sync Successfully!',
        ]);
    }



    public function show(string $id)
    {
        $user = User::with('roles')->find($id);


        return response()->json([
            'status' => 200,
            'message' => 'User fetched successfully',
            'user' => $user,
            'user_roles' => $user->roles()->pluck('name'),
            'roles' => Role::get()
        ]);
    }

    public function edit(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);

        $user = User::find($id);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $this->deleteImage($user->image);
            $data['image'] = $this->uploadImage($request->file('image'), 'user');
        }


        $data['password'] = Hash::make($request->password);
        // $data['role'] = 'admin';

        $user->update($data);

        $user->syncRoles($request->roles);

        return response()->json([
            'status' => 200,
            'message' => 'User & Role Sync and updated Successfully!',
        ]);
    }

    // public function update(Request $request, string $id)
    // {
    //     $user = User::findOrFail($id);

    //     $validated = $request->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'email', Rule::unique('users')->ignore($user->id),],
    //         'phone' => ['nullable', 'string', 'max:20'],
    //         'password' => ['nullable', 'min:5', 'max:15'],
    //         'roles' => ['required', 'array'],
    //         'roles.*' => ['exists:roles,name'],
    //     ]);

    //     // Upload image
    //     if ($request->hasFile('image')) {
    //         if ($user->image) {
    //             $this->deleteImage($user->image);
    //         }

    //         $validated['image'] = $this->uploadImage($request->file('image'), 'user');
    //     }

    //     // Update password only if provided
    //     if (!empty($request->password)) {
    //         $validated['password'] = Hash::make($request->password);
    //     } else {
    //         unset($validated['password']);
    //     }

    //     // Remove roles before updating user table
    //     unset($validated['roles']);

    //     $user->update($validated);

    //     // Sync roles
    //     $user->syncRoles($request->roles);

    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'User updated successfully.',
    //         'user' => $user->load('roles'),
    //     ]);
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if ($user->image) {
            $this->deleteImage($user->image);
        }
        $user->delete();

        return response()->json([
            'status' => 200,
            'message' => $this->panel . ' deleted successfully !!'
        ]);
    }
}
