
<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

if (!function_exists('get_directory_size')) {
    /**
     * Helper function to get directory size.
     *
     * @param string $path
     * @return int
     */
    function get_directory_size($path)
    {
        $size = 0;
        try {
            if (File::exists($path) && File::isDirectory($path)) {
                foreach (File::allFiles($path) as $file) {
                    $size += $file->getSize();
                }
            }
        } catch (Exception $e) {
            Log::warning('Could not get directory size for ' . $path . ': ' . $e->getMessage());
            return 0;
        }
        return $size;
    }
}
