<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Exception;

class MediaService
{
    /**
     * Uploads a file and stores its information in the database.
     *
     * @param UploadedFile $file The file to upload.
     * @param Model $model The model associated with the media (e.g., Post).
     * @param string $folder The folder inside the disk (e.g., 'thumbnails', 'post_content_images').
     * @param string $disk The storage disk (e.g., 'public').
     * @return Media|null The created Media model instance or null on failure.
     */
    public function uploadFile(UploadedFile $file, Model $model, string $folder = 'uploads', string $disk = 'public'): ?Media
    {
        try {
            $originalFileName = $file->getClientOriginalName();
            $path = $file->store($folder, $disk); // Laravel tự động tạo tên file duy nhất

            $media = $model->media()->create([
                'file_name' => $originalFileName,
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'disk' => $disk,
            ]);

            return $media;
        } catch (Exception $e) {
            // Log the error for debugging
            \Log::error("Failed to upload file: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Deletes a media file and its database record.
     *
     * @param Media $media The Media model instance to delete.
     * @return bool True if successful, false otherwise.
     */
    public function deleteMedia(Media $media): bool
    {
        try {
            Storage::disk($media->disk)->delete($media->file_path);
            $media->delete();
            return true;
        } catch (Exception $e) {
            \Log::error("Failed to delete media: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all media files from the database.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllMedia()
    {
        return Media::all();
    }

    /**
     * Get total file count.
     *
     * @return int
     */
    public function getTotalFileCount(): int
    {
        return Media::count();
    }

    /**
     * Get total storage size used by media files.
     *
     * @return int Total size in bytes.
     */
    public function getTotalStorageSize(): int
    {
        return Media::sum('file_size');
    }

    /**
     * Clears all media data from storage and database.
     *
     * @return bool True if successful, false otherwise.
     */
    public function clearAllMedia(): bool
    {
        try {
            // Lấy tất cả media records
            $allMedia = Media::all();

            // Xóa file từ storage
            foreach ($allMedia->groupBy('disk') as $diskName => $mediaOnDisk) {
                $paths = $mediaOnDisk->pluck('file_path')->toArray();
                Storage::disk($diskName)->delete($paths);
            }

            // Xóa records khỏi database
            Media::truncate();
            return true;
        } catch (Exception $e) {
            \Log::error("Failed to clear all media: " . $e->getMessage());
            return false;
        }
    }
}
