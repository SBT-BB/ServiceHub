<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class FileService
{
    /**
     * Upload a file and return its path.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $oldPath
     * @param string|null $prefix
     * @return string
     */
    public function upload(UploadedFile $file, string $directory, ?string $oldPath = null, ?string $prefix = null): string
    {
        // Delete old file if exists
        $this->delete($oldPath);

        $path = public_path($directory);
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $filename = ($prefix ? $prefix . '_' : '') . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($path, $filename);

        return $directory . '/' . $filename;
    }

    /**
     * Delete a file from public path.
     *
     * @param string|null $path
     * @return bool
     */
    public function delete(?string $path): bool
    {
        if ($path && File::exists(public_path($path))) {
            return File::delete(public_path($path));
        }

        return false;
    }
}
