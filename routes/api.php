<?php

use App\Http\Controllers\API\AboutController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\CompanyDeviceController;
use App\Http\Controllers\API\DesignationController;
use App\Http\Controllers\API\DeviceBrandController;
use App\Http\Controllers\API\DeviceController;
use App\Http\Controllers\API\GenderController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\StaffController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VendorController;
use App\Http\Controllers\API\AttendanceController;
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
    Route::apiResource('device-brand', DeviceBrandController::class);
    Route::apiResource('devices', DeviceController::class);


    Route::apiResource('company-devices', CompanyDeviceController::class);
    Route::get('company-devices/restore/{id}', [CompanyDeviceController::class, 'restore'])->name('company_devices.restore');
    Route::delete('company-devices/permanent/{id}', [CompanyDeviceController::class, 'destroyPermanent'])->name('company_devices.delete_permanent');

    // Staffs
    Route::apiResource('staffs', StaffController::class);
    Route::get('staffs/restore/{id}', [StaffController::class, 'restore'])->name('company_devices.restore');
    Route::delete('staffs/permanent/{id}', [StaffController::class, 'destroyPermanent'])->name('company_devices.delete_permanent');


    // Attendances
    Route::apiResource('attendances',  AttendanceController::class);
    Route::get('attendances/restore/{id}', [AttendanceController::class, 'restore'])->name('company_devices.restore');
    Route::delete('attendances/permanent/{id}', [AttendanceController::class, 'destroyPermanent'])->name('company_devices.delete_permanent');
    Route::get('attendance/search-by-date', [AttendanceController::class, 'searchByDate'])->name('attendance.searchByDate');

    Route::apiResource('permissions', PermissionController::class);
    // Vendor 
    Route::apiResource('vendors', VendorController::class);
    Route::get('vendors/restore/{id}', [VendorController::class, 'restore'])->name('vendor.restore');
    Route::delete('vendors/destroy/{id}', [VendorController::class, 'destroyPermanent'])->name('vendor.delete_permanent');

    // Company 
    Route::apiResource('companys', CompanyController::class);
    Route::get('companys/restore/{id}', [CompanyController::class, 'restore'])->name('company.restore');
    Route::delete('companys/destroy/{id}', [CompanyController::class, 'destroyPermanent'])->name('company.delete_permanent');

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
