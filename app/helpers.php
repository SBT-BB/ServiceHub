<?php

if (!function_exists('getSettings')) {
    function getSettings($key = null, $id = null)
    {
        // Return a dummy object to prevent errors in views
        return (object) [
            'logo' => asset('assets/images/light-logo.png'),
            'site_name' => 'Herozi',
            'favicon' => asset('assets/images/light-logo.png'),
        ];
    }
}
if (!function_exists('uploadFile')) {
    function uploadFile($file, $directory, $oldPath = null, $prefix = null)
    {
        return app(\App\Services\FileService::class)->upload($file, $directory, $oldPath, $prefix);
    }
}

if (!function_exists('deleteFile')) {
    function deleteFile($path)
    {
        return app(\App\Services\FileService::class)->delete($path);
    }
}
