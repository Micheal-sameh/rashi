<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SsoLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // A Passport-style RS256 JWT is ~1–2 KB; 4 KB leaves headroom
            // without letting an arbitrarily large body reach the SSO call.
            'access_token' => 'required|string|max:4096',
            'fcm_token' => 'sometimes|string|max:255',
            'device_type' => 'sometimes|string|in:ios,android,web',
            'imei' => 'sometimes|string|max:255',
        ];
    }
}
