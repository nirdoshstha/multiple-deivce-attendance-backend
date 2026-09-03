<?php

use App\Http\Controllers\API\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('frontend.index');


// Biometric attendance (ZKT eco)
Route::any('iclock/{any}', [AttendanceController::class, 'doGet']);
