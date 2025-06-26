<?php

use App\Http\Controllers\Chatbot\HomeController;
use App\Http\Controllers\Chatbot\SettingController;
use App\Http\Controllers\Chatbot\SubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('chatbot')->name('chatbot.')->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('index');

        Route::prefix('setting')->name('setting.')->group(function () {
            Route::get('/', [SettingController::class, 'edit'])->name('index');
            Route::patch('/', [SettingController::class, 'update'])->name('update');
        });

        // Routes subjects
        Route::resource('subjects', SubjectController::class);
    });
});
