<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SystemSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logo' => [
                'nullable',
                'file',
                'max:2048', // 2MB
            ],
            'favicon' => [
                'nullable',
                'file',
                'max:1024', // 1MB
            ],
            'footer_text' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'logo.max' => 'Logo may not be greater than 2MB.',

            'favicon.max' => 'Favicon may not be greater than 1MB.',
        ];
    }
}
