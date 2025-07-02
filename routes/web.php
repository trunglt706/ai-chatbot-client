<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BackupController;
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
use Illuminate\Support\Facades\Artisan;
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
    Route::middleware('throttle:10,1')->group(function () {
        Route::get('command', function () {
            try {
                $text = request('text');
                Artisan::call(trim($text));
                return "OK";
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        });
    });

    // Routes cho dashboard
    Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Routes cho profile
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/security', function () {
            return view('profile.two-factor-authentication');
        })->name('profile.security');
    });

    // Routes cho quản lý người dùng
    Route::resource('users', UserController::class);
    Route::delete('/users/{user}/sessions/{sessionId}', [UserController::class, 'destroySession'])->name('users.sessions.destroy');
    Route::post('/users/{user}/logout-all-devices', [UserController::class, 'logoutAllDevices'])->name('users.logout-all-devices');

    // Routes cho quản lý vai trò
    Route::get('roles/order', [RoleController::class, 'order'])->name('roles.order');
    Route::resource('roles', RoleController::class);
    Route::post('roles/update-nestable-order', [RoleController::class, 'updateNestableOrder'])->name('roles.updateNestableOrder');

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
    Route::get('posts/{slug}', [PostController::class, 'show'])->name('posts.show');

    // Routes đọc thông báo
    Route::prefix('notifications')->group(function () {
        Route::post('/{notification}/read', [NotificationContronller::class, 'markAsRead']);
        Route::post('/read-all', [NotificationContronller::class, 'markAllAsRead']);
    });

    // Quản lý Dung lượng Hệ thống
    Route::prefix('storage-report')->group(function () {
        Route::get('', [StorageController::class, 'index'])->name('storage.index');
        Route::delete('/clear-all', [StorageController::class, 'clearAll'])->name('storage.clear-all');
        Route::delete('/{media}', [StorageController::class, 'destroy'])->name('storage.destroy');
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
        Route::post('/create_storage_link', [SystemController::class, 'createStorageLink'])->name('system.create_storage_link');
    });

    // Routes cho Quản lý Nhà tài trợ
    Route::resource('sponsors', SponsorController::class);
    Route::post('sponsors/{sponsor}/attachModule', [SponsorController::class, 'attachModule'])->name('sponsors.attachModule');
    Route::delete('sponsors/{sponsor}/modules/{module}/cancel', [SponsorController::class, 'cancelModuleSponsorship'])->name('sponsors.cancelModuleSponsorship');

    Route::prefix('backups')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/run', [BackupController::class, 'runManualBackup'])->name('backup.run');
        Route::get('/download/{fileName}', [BackupController::class, 'downloadBackup'])
            ->where('fileName', '.*')
            ->name('backups.download');
        Route::delete('/delete/{fileName}', [BackupController::class, 'deleteBackup'])
            ->where('fileName', '.*')
            ->name('backups.delete');
    });

    require __DIR__ . '/chatbot.php';
});

require __DIR__ . '/auth.php';
