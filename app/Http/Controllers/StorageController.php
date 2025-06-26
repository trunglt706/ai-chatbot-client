<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\MediaService;
use App\Models\Media;

class StorageController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->middleware(['auth', 'can:manage storage']); // Cần quyền mới
        $this->mediaService = $mediaService;
    }

    /**
     * Display the storage management dashboard.
     */
    public function index()
    {
        $totalFiles = $this->mediaService->getTotalFileCount();
        $totalSizeInBytes = $this->mediaService->getTotalStorageSize();
        $allMediaFiles = $this->mediaService->getAllMedia(); // Lấy tất cả media để hiển thị chi tiết

        $totalSizeReadable = $this->formatBytes($totalSizeInBytes);

        return view('storage.index', compact('totalFiles', 'totalSizeReadable', 'allMediaFiles'));
    }

    /**
     * Delete a specific media file.
     */
    public function destroy(Media $media)
    {
        if ($this->mediaService->deleteMedia($media)) {
            return redirect()->route('storage.index')->with('success', 'Tập tin đã được xóa thành công!');
        }
        return redirect()->back()->with('error', 'Không thể xóa tập tin.');
    }

    /**
     * Clear all media data.
     */
    public function clearAll(Request $request)
    {
        if ($this->mediaService->clearAllMedia()) {
            return redirect()->route('storage.index')->with('success', 'Tất cả dữ liệu media đã được xóa thành công!');
        }
        return redirect()->back()->with('error', 'Không thể xóa tất cả dữ liệu media.');
    }

    /**
     * Helper to format bytes into human readable format.
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
