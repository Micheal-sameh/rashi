<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SsoLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'access_token' => 'required|string',
            'fcm_token' => 'sometimes|string',
            'device_type' => 'sometimes|string|in:ios,android,web',
            'imei' => 'sometimes|string',
        ];
    }
}
