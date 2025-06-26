<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage system');
    }

    /**
     * Display a listing of the system settings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $storageLinkExists = File::exists(public_path('storage'));
        $publicStoragePath = storage_path('app/public');
        $publicStorageSize = 'N/A';
        if (File::exists($publicStoragePath) && File::isDirectory($publicStoragePath)) {
            // Gọi helper function từ FileHelper
            $publicStorageSize = format_size_units(get_directory_size($publicStoragePath));
        }

        // Gọi helper functions từ TestHelper
        $broadcastConfigStatus = check_broadcast_config();
        $emailConfigStatus = check_email_config();
        $slackConfigStatus = check_slack_config();

        $emailTestResult = session('email_test_result');
        $slackTestResult = session('slack_test_result');
        $broadcastTestResult = session('broadcast_test_result');

        return view('system.index', compact(
            'storageLinkExists',
            'publicStorageSize',
            'broadcastConfigStatus',
            'emailConfigStatus',
            'slackConfigStatus',
            'emailTestResult',
            'slackTestResult',
            'broadcastTestResult'
        ));
    }

    // --- CÁC PHƯƠNG THỨC HÀNH ĐỘNG HỆ THỐNG ---

    public function createStorageLink(Request $request)
    {
        try {
            Artisan::call('storage:link');
            return redirect()->route('system.index')->with('success', 'Symbolic link đã được tạo thành công.');
        } catch (Exception $e) {
            Log::error('Failed to create storage link: ' . $e->getMessage());
            return redirect()->route('system.index')->with('error', 'Không thể tạo symbolic link: ' . $e->getMessage());
        }
    }

    public function clearAllCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('optimize:clear');

            return redirect()->route('system.index')->with('success', 'Toàn bộ cache hệ thống đã được xóa thành công.');
        } catch (Exception $e) {
            Log::error('Failed to clear cache: ' . $e->getMessage());
            return redirect()->route('system.index')->with('error', 'Không thể xóa cache hệ thống: ' . $e->getMessage());
        }
    }

    // --- CÁC PHƯƠNG THỨC KIỂM TRA HOẠT ĐỘNG THỰC TẾ (Test Button Actions) ---

    public function testBroadcast(Request $request)
    {
        // Gọi helper function từ TestHelper
        $result = test_broadcast_service();
        return redirect()->route('system.index')->with('broadcast_test_result', $result);
    }

    public function testEmail(Request $request)
    {
        $request->validate(['email_recipient' => 'required|email']);
        $recipient = $request->email_recipient;

        // Gọi helper function từ TestHelper
        $result = test_email_service($recipient);
        return redirect()->route('system.index')->with('email_test_result', $result);
    }

    public function testSlack(Request $request)
    {
        // Gọi helper function từ TestHelper
        $result = test_slack_service();
        return redirect()->route('system.index')->with('slack_test_result', $result);
    }
}
