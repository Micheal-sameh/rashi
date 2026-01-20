<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuestionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'question' => 'nullable|string|required_without:question_image|prohibited_if:question_image,!=,null',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048|required_without:question|prohibited_if:question,!=,null',
            'quiz_id' => 'required|integer|exists:quizzes,id',
            'points' => 'required|integer',
            'answers' => 'required|array|min:2|max:4',
            'answers.*' => 'required|string',
            'correct' => 'required|integer|min:1|max:'.(count($this->input('answers', [])) ?: 4),
        ];
    }
}
