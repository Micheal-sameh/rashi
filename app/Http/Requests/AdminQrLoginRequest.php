<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminQrLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'qr_code' => 'required|string',
            'remember' => 'sometimes|boolean',
        ];
    }
}
