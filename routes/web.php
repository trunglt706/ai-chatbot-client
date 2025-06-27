<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\NotificationContronller;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestFormController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('captcha/flat', function () {
    return captcha_img();
})->name('captcha.flat');

Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'vi'])) {
        Session::put('locale', $locale);
        App::setLocale($locale);
    }
    return redirect()->back(); // Chuyển hướng về trang trước đó
})->name('change.language');

Route::middleware(['auth', 'verified'])->group(function () {
    // Routes cho dashboard
    Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Routes cho profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Routes cho quản lý người dùng
    Route::resource('users', UserController::class);

    // Routes cho quản lý vai trò
    Route::resource('roles', RoleController::class);

    // Routes cho quản lý module
    Route::resource('modules', ModuleController::class)->only(['index', 'edit', 'update', 'show']);

    // Routes cho quản lý activity logs
    Route::resource('activity-logs', ActivityLogController::class)->only(['index']);

    // Route cho Log Viewer (đảm bảo chỉ admin mới có thể truy cập)
    Route::get('log-storage', [LogViewerController::class, 'index'])->name('log-viewer')->middleware('can:view log storage');

    // Routes cho gửi yêu cầu
    Route::resource('request-forms', RequestFormController::class);

    // Routes cho bài viết
    Route::resource('posts', PostController::class);

    // Routes đọc thông báo
    Route::prefix('notifications')->group(function () {
        Route::post('/{notification}/read', [NotificationContronller::class, 'markAsRead']);
        Route::post('/read-all', [NotificationContronller::class, 'markAllAsRead']);
    });

    // Quản lý Dung lượng Hệ thống
    Route::prefix('storage')->group(function () {
        Route::get('/report', [StorageController::class, 'index'])->name('storage.index');
        Route::delete('/{media}', [StorageController::class, 'destroy'])->name('storage.destroy');
        Route::post('/clear-all', [StorageController::class, 'clearAll'])->name('storage.clear-all');
    });

    // Quản lý Hệ thống
    Route::middleware(['can:manage system'])->prefix('system')->group(function () {
        Route::get('', [SystemController::class, 'index'])->name('system.index');
        Route::delete('/data/{table_name}', [SystemController::class, 'deleteTableData'])->name('system.delete_table_data');
        Route::post('/clear-all-data', [SystemController::class, 'clearAllCache'])->name('system.clear_all_data');

        // NEW Routes for testing services
        Route::post('/test-broadcast', [SystemController::class, 'testBroadcast'])->name('system.test_broadcast');
        Route::post('/test-email', [SystemController::class, 'testEmail'])->name('system.test_email');
        Route::post('/test-slack', [SystemController::class, 'testSlack'])->name('system.test_slack');
    });

    // Routes cho Quản lý Nhà tài trợ
    Route::resource('sponsors', SponsorController::class);
});

require __DIR__ . '/auth.php';
require __DIR__ . '/chatbot.php';
