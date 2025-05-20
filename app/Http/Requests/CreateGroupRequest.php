<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGroupRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => "required|string|unique:groups,name,{$this->route('id')}",
            'users' => 'array',
            'users.*' => 'integer|exists:users,id',
        ];
    }
}
