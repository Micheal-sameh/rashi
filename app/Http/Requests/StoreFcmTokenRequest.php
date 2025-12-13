<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFcmTokenRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => 'required|string',
            'device_type' => 'string|in:ios,android,web',
            'imei' => 'required|string',
        ];
    }
}
