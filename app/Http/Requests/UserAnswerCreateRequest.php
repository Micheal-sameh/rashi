<?php

namespace App\Http\Requests;

use App\Rules\CanSolveQuizRule;
use Illuminate\Foundation\Http\FormRequest;

class UserAnswerCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'quiz_id' => ['required', 'integer', 'exists:quizzes,id', new CanSolveQuizRule],
            'questions' => 'required|array',
            'questions.*.question_id' => [
                'required',
                'integer',
                'exists:quiz_questions,id',
                function ($attribute, $value, $fail) {
                    $quizId = $this->input('quiz_id');
                    $question = \App\Models\QuizQuestion::where('id', $value)->where('quiz_id', $quizId)->first();
                    if (! $question) {
                        $fail('The selected question does not belong to the specified quiz.');
                    }
                },
            ],
            'questions.*.answer_id' => 'required|integer|exists:question_answers,id',
        ];
    }
}
