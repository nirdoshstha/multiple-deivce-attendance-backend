<?php

use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\Api\IclockController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('frontend.index');


// Biometric attendance (ZKT eco)
// Route::any('iclock/{any}', [AttendanceController::class, 'doGet']);

// PUBLIC — no auth:sanctum. The physical device calls these directly, over
// plain HTTP(S), identifying itself via the SN query param. Keep this
// throttle generous but present: real devices poll every 10-30s.
Route::middleware(['throttle:120,1'])->group(function () {
    Route::match(['get', 'post'], '/iclock/cdata', [IclockController::class, 'cdata']);
    Route::get('/iclock/getrequest', [IclockController::class, 'getRequest']);
    Route::post('/iclock/devicecmd', [IclockController::class, 'deviceCmd']);
});
