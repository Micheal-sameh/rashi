<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupUsers extends FormRequest
{
    public function rules(): array
    {
        return [
            'users' => 'array',
            'users.*' => 'integer|exists:users,id',
        ];
    }
}
