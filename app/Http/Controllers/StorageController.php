<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StorageController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'permission:report log storage'])->only('index', 'destroy', 'clearAll');
    }

    /**
     * Display the storage management dashboard.
     */
    public function index()
    {
        $pageSize = request('pageSize', 10);

        $totalFiles = Media::count();
        $totalSizeInBytes = Media::sum('size');
        $allMediaFiles = Media::paginate($pageSize);
        $totalSizeReadable = format_size_units($totalSizeInBytes);

        return view('storage.index', compact('totalFiles', 'totalSizeReadable', 'allMediaFiles'));
    }

    /**
     * Delete a specific media file.
     */
    public function destroy(Media $media)
    {
        try {
            $media->delete();
            return redirect()->route('storage.index')->with('success', __('The file has been deleted successfully!'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Unable to delete the file.'));
        }
    }

    /**
     * Clear all media data.
     */
    public function clearAll(Request $request)
    {
        try {
            Media::all()->each(function ($media) {
                $media->delete();
            });
            return redirect()->route('storage.index')->with('success', __('All media data has been deleted successfully!'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Unable to delete all media data.'));
        }
    }
}
