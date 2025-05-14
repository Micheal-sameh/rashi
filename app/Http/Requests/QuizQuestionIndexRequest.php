<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuizQuestionIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'competition_id' => 'integer',
            'quiz_id' => 'integer',
        ];
    }
}
