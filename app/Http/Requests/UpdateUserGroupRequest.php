<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserGroupRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'groups' => 'required|array',
            'groups.*' => 'integer|exists:groups,id',
        ];
    }
}
