<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'membership_code' => 'required|exists:users,membership_code',
            'password' => 'required',
            'remember_me' => 'boolean',
        ];
    }
}
