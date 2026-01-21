<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuestionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'question' => 'nullable|string|required_without:question_image|prohibits:question_image',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048|required_without:question|prohibits:question',
            'quiz_id' => 'required|integer|exists:quizzes,id',
            'points' => 'required|integer|min:1',
            'answers' => 'required|array|min:2|max:4',
            'answers.*' => 'required|string',
            'correct' => 'required|integer|min:1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $answersCount = count($this->input('answers', []));
            $correct = $this->input('correct');

            if ($correct > $answersCount) {
                $validator->errors()->add('correct',
                    "The correct answer index cannot be greater than {$answersCount}."
                );
            }
        });
    }
}
