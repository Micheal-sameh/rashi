<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'competition_id' => 'required|integer',
        ];
    }
}
