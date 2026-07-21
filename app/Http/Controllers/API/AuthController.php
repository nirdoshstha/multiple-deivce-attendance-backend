<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends BackendBaseController
{

    protected $model;
    protected $panel = 'User';
    protected $img_path = 'uploads/user/';

    public function __construct()
    {
        $this->model = new User();
    }

    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:5|max:15',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'User Registered Successfully!',
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        // $user = User::where('email', $request->email)->first();
        $user = User::with(['roles', 'permissions'])
            ->where('email', $request->email)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 200,
            'message' => 'User Login Successfully!',
            'token' => $token,
            'user' => $user,
            // User Roles
            'roles' => $user->getRoleNames(),

            // Direct + Role Permissions
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),

            // Optional full role objects
            'roles_data' => $user->roles,
        ]);
    }

    public function users(Request $request)
    {
        // $users = User::where('role', '!=', 'superadmin')->get();
        $users = User::whereIn('role', ['admin', 'superadmin', 'user'])->get();
        return response()->json([
            'users' => $users
        ]);
    }

    public function storeUser(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,except,id',
            'phone' => 'required',
        ]);


        try {
            $data = $request->except('image');

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->file('image'), 'user');
            }


            $data['password'] = Hash::make('12345');
            $data['role'] = 'admin';

            User::create($data);

            return response()->json([
                'status' => 200,
                'message' => 'New Admin Created Successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong!',
            ]);
        }
    }

    public function deleteUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->image) {
            $this->deleteImage($user->image);
        }
        $user->delete();

        return response()->json([
            'status' => 200,
            'message' => 'User deleted successfully!'
        ]);
    }

    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, auth('sanctum')->user()->password)) {
                        $fail('The old password is incorrect.');
                    }
                },
            ],
            'new_password' => 'required|min:5',
            'cnew_password' => 'required|same:new_password',
        ], [
            'new_password.required' => 'Please enter a new password.',
            'new_password.min' => 'The new password must be at least 5 characters.',
            'cnew_password.required' => 'Please confirm your new password.',
            'cnew_password.same' => 'The new passwords do not match.',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Password updated successfully!',
        ]);
    }

    // public function updateUser(Request $request, $id)
    // {
    //     $user = User::findOrFail($id);
    //     $request->validate([
    //         'name'  => 'required',
    //         'email' => 'required|email|unique:users,email,' . $user->id,
    //         'role'  => 'required|in:superadmin,admin,user',
    //         'phone' => 'nullable',
    //     ]);

    //     // Fill the model with the new values
    //     $user->fill([
    //         'name'  => $request->name,
    //         'email' => $request->email,
    //         'image' => $this->uploadImage($request->file('image'), 'user'),
    //         'role'  => $request->role,
    //         'phone' => $request->phone,
    //         'user' => $user
    //     ]);

    //     // Check if anything actually changed
    //     if (! $user->isDirty()) {
    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Nothing to change.',
    //             'user' => $user,
    //         ]);
    //     }

    //     // Save changes
    //     $user->save();

    //     // Refresh model
    //     $user->refresh();

    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'User updated successfully!',
    //         'user' => $user,
    //     ]);
    // }

    public function updateUser(Request $request, $id)
    {

        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:superadmin,admin,user',
            'phone' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Update basic fields
        $user->name  = $request->name;
        $user->email = $request->email;
        $user->role  = $request->role;
        $user->phone = $request->phone;

        // Update image only if a new one is uploaded
        if ($request->hasFile('image')) {

            // Delete old image
            if ($user->image) {
                $this->deleteImage($user->image);
            }

            // Upload new image
            $user->image = $this->uploadImage($request->file('image'), 'user');
        }

        // Check if anything changed
        if (! $user->isDirty()) {
            return response()->json([
                'status' => 200,
                'message' => 'Nothing to change.',
                'user' => $user,
            ]);
        }

        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'User updated successfully!',
            'user' => $user,
        ]);
    }
}
