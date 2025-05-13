<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProfilePicRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'image' => 'required|file|mimes:png,jpg,jpeg|max:2048',
        ];
    }
}
