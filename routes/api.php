<?php

use App\Http\Controllers\API\AboutController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DesignationController;
use App\Http\Controllers\API\GenderController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    $user = $request->user();

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'phone' => $user->phone,
        'image' => $user->image,
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getAllPermissions()->pluck('name'),
    ]);
})->middleware('auth:sanctum')->name('auth.user');

Route::post('/register', [AuthController::class, 'register'])->name('user.register');

Route::post('/login', [AuthController::class, 'login'])->name('user.login');

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('genders', GenderController::class);
    Route::apiResource('designations', DesignationController::class);
    // Route::apiResource('permissions', PermissionController::class);

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/users/update-password/{id}', [AuthController::class, 'updatePassword']);

    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');
    Route::get('/settings', [SettingController::class, 'show'])->name('settings.show');

    Route::get('/about', [AboutController::class, 'index'])->name('about.index');
    Route::post('/about/store', [AboutController::class, 'store'])->name('about.store');
    Route::post('/about-post/store', [AboutController::class, 'storePost'])->name('abouts.index');
    Route::get('/about-post/{id}', [AboutController::class, 'editPost'])->name('abouts.edit');
    Route::post('/about-post/update/{id}', [AboutController::class, 'updatePost'])->name('abouts.update');

    Route::post('/about/status/{id}', [AboutController::class, 'statusPost'])->name('about.status');
    Route::delete('/about-post/{id}', [AboutController::class, 'destroy'])->name('abouts.destroy');
});
