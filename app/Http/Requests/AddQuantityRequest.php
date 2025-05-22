<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddQuantityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'quantity' => 'required|numeric|gt:0',
        ];
    }
}
