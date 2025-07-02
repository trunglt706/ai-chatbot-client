<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\BackupDestination\BackupDestinationFactory;
use Spatie\Backup\Config\Config;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;

class BackupController extends Controller
{
    /**
     * Hiển thị danh sách các bản backup.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $config = Config::fromArray(config('backup'));

        $backupStatuses = BackupDestinationStatusFactory::createForMonitorConfig($config->monitoredBackups);

        $backups = [];

        foreach ($backupStatuses as $status) {
            $backup = $status->backupDestination()->newestBackup();

            if (! $backup) {
                continue;
            }

            $backups[] = [
                'name' => basename($backup->path()),
                'path' => $backup->path(),
                'size' => $backup->sizeInBytes(),
                'date' => $backup->date(),
                'disk' => $status->backupDestination()->diskName(),
                // 'type' => in_array('manual', $backup->tags()) ? 'Manual' : 'Auto',
            ];
        }

        usort($backups, fn($a, $b) => $b['date']->timestamp <=> $a['date']->timestamp);

        return view('backups.index', compact('backups'));
    }

    /**
     * Chạy backup thủ công và gán tag "manual".
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function runManualBackup(Request $request)
    {
        try {
            // Chạy lệnh backup với tag "manual"
            Artisan::call('backup:run', ['--only-db' => true]);

            $output = Artisan::output();
            Log::info('Manual backup successful: ' . $output);

            return redirect()->route('backups.index')->with('success', __('Manual backup created successfully!'));
        } catch (\Exception $e) {
            Log::error('Manual backup failed: ' . $e->getMessage());
            return redirect()->route('backups.index')->with('error', __('Manual backup failed: ') . $e->getMessage());
        }
    }

    /**
     * Tải xuống một bản backup.
     *
     * @param  string  $fileName
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function downloadBackup($fileName)
    {
        // Spatie backup thường lưu trong thư mục con với tên ứng dụng
        $backupPath = config('backup.backup.name') . '/' . $fileName;
        $diskName = config('backup.destination.disks')[0] ?? 'local'; // Lấy disk mặc định

        if (!Storage::disk($diskName)->exists($backupPath)) {
            return redirect()->back()->with('error', __('Backup file not found.'));
        }

        return Storage::disk($diskName)->download($backupPath);
    }

    /**
     * Xóa một bản backup.
     *
     * @param  string  $fileName
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteBackup(Request $request, $fileName)
    {
        // Spatie backup thường lưu trong thư mục con với tên ứng dụng
        $backupPath = config('backup.backup.name') . '/' . $fileName;
        $diskName = config('backup.destination.disks')[0] ?? 'local'; // Lấy disk mặc định

        if (!Storage::disk($diskName)->exists($backupPath)) {
            return redirect()->back()->with('error', __('Backup file not found.'));
        }

        try {
            Storage::disk($diskName)->delete($backupPath);
            Log::info('Backup deleted: ' . $backupPath);
            return redirect()->back()->with('success', __('Backup deleted successfully.'));
        } catch (\Exception $e) {
            Log::error('Failed to delete backup: ' . $e->getMessage());
            return redirect()->back()->with('error', __('Failed to delete backup:') . $e->getMessage());
        }
    }
}
