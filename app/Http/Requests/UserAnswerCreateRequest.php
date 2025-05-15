<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAnswerCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'quiz_id' => 'required|integer|exists:quizzes,id',
            'questions' => 'required|array',
            'questions.*.question_id' => 'required|integer|exists:quiz_questions,id',
            'questions.*.answer_id' => 'required|integer|exists:question_answers,id',
        ];
    }
}
