<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogoutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'fcm_token' => 'nullable|string|max:255',
            'device_type' => 'sometimes|string|in:ios,android,web',
            'imei' => 'sometimes|string|max:255',
        ];
    }
}
