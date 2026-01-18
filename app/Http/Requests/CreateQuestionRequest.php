<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuestionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'question' => 'required|string',
            'quiz_id' => 'required|integer|exists:quizzes,id',
            'points' => 'required|integer',
            'answers' => 'required|array|min:2|max:4',
            'answers.*' => 'required|string',
            'correct' => 'required|integer|min:1|max:'.(count($this->input('answers', [])) ?: 4),
        ];
    }
}
