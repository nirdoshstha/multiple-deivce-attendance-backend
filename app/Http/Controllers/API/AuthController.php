<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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

    // public function register(Request $request)
    // {

    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|unique:users,email',
    //         'password' => 'required|min:5|max:15',
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'User Registered Successfully!',
    //     ]);
    // }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        try {
            // $user = User::where('email', $request->email)->first();
            $user = User::with(['roles'])
                ->where('email', $request->email)
                ->first();

            $permissions = $user->getAllPermissions()->pluck('name')->toArray();

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
                'user' => [
                    ...$user->toArray(),
                    'permissions' => $permissions
                ],
                // User Roles
                'roles' => $user->getRoleNames(),

                // Direct + Role Permissions
                'permissions' => $permissions,


            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
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

    public function profile(Request $request, string $id)
    {

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:5|max:15',
            'image' => 'nullable|max:2048',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        // Upload image
        if ($request->hasFile('image')) {

            if ($user->image) {
                $this->deleteImage($user->image);
            }

            $validated['image'] = $this->uploadImage($request->file('image'), 'user');
        }

        //Update password only if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Update user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
            'password' => $validated['password'] ?? $user->password,
            'image' => $validated['image'] ?? $user->image,
        ]);

        // Update roles only if roles were sent
        if ($request->filled('roles')) {
            $user->syncRoles($request->roles);
        }

        return response()->json([
            'status' => 200,
            'message' => 'User updated successfully.',
            'user' => $user->fresh(), //$user->load('roles'),
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

    // public function updateUser(Request $request, $id)
    // {

    //     $user = User::findOrFail($id);

    //     $request->validate([
    //         'name'  => 'required',
    //         'email' => 'required|email|unique:users,email,' . $user->id,
    //         'role'  => 'required|in:superadmin,admin,user',
    //         'phone' => 'nullable',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    //     ]);

    //     // Update basic fields
    //     $user->name  = $request->name;
    //     $user->email = $request->email;
    //     $user->role  = $request->role;
    //     $user->phone = $request->phone;

    //     // Update image only if a new one is uploaded
    //     if ($request->hasFile('image')) {

    //         // Delete old image
    //         if ($user->image) {
    //             $this->deleteImage($user->image);
    //         }

    //         // Upload new image
    //         $user->image = $this->uploadImage($request->file('image'), 'user');
    //     }

    //     // Check if anything changed
    //     if (! $user->isDirty()) {
    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Nothing to change.',
    //             'user' => $user,
    //         ]);
    //     }

    //     $user->save();

    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'User updated successfully!',
    //         'user' => $user,
    //     ]);
    // }
}
