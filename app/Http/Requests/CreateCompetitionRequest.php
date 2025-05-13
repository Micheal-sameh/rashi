<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCompetitionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'start_at' => 'required|date|after_or_equal:today',
            'end_at' => 'required|date|after_or_equal:start_at',
            'image' => 'image|mimes:png,jpg'.($this->route('id') == null ? '|required' : ''),
        ];
    }
}
