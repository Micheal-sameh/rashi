<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
        ];
    }
}
