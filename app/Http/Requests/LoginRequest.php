<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'qr_code' => 'required|string',
            'fcm_token' => 'sometimes|string',
            'device_type' => 'sometimes|string|in:ios,android,web',
            'imei' => 'sometimes|string',
            // 'name' => 'required|string',
            // 'email' => 'unique|users,email|email',
            // 'phone' => 'unique|users,phone|string',
            // 'membership_code' => 'required|string',
            // 'password' => 'string',
        ];
    }
}
