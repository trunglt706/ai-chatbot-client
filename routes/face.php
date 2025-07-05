<?php

use App\Http\Controllers\Face\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::prefix('face')->name('face.')->group(function () {
    Route::post('/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/register-face', [AttendanceController::class, 'registerFace']);
});
