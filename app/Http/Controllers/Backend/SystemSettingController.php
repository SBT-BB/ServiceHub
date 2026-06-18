<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\SystemSettingRequest;
use App\Models\Setting;
use App\Services\FileService;

class SystemSettingController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function edit()
    {
        $settings = [
            'logo' => Setting::get('logo'),
            'favicon' => Setting::get('favicon'),
            'footer_text' => Setting::get('footer_text'),
        ];

        return view('Backend.Settings.General', compact('settings'));
    }

    public function update(SystemSettingRequest $request)
    {
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('logo');
            $path = $this->fileService->upload($request->file('logo'), 'uploads/settings', $oldLogo, 'logo');
            Setting::set('logo', $path);
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $oldFavicon = Setting::get('favicon');
            $path = $this->fileService->upload($request->file('favicon'), 'uploads/settings', $oldFavicon, 'favicon');
            Setting::set('favicon', $path);
        }

        if ($request->filled('footer_text')) {
            Setting::set('footer_text', $request->footer_text);
        }

        return redirect()->route('settings.edit')->with('success', 'Settings updated successfully.');
    }
}
