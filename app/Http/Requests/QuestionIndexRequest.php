<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'quiz_id' => 'required|integer',
        ];
    }
}
