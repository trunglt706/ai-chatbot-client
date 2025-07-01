<?php

use App\Http\Controllers\Chatbot\HomeController;
use App\Http\Controllers\Chatbot\SettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('chatbot')->name('chatbot.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('index');
    Route::get('demo', [HomeController::class, 'demo'])->name('demo');
    Route::post('send', [HomeController::class, 'send'])->name('send');
    Route::get('history', [HomeController::class, 'history'])->name('history');
    Route::post('speech-to-text', [HomeController::class, 'speechToText'])->name('speech-to-text');

    Route::prefix('setting')->name('setting.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/', [SettingsController::class, 'update'])->name('update');
    });
});
