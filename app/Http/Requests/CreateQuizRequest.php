<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuizRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'competition_id' => 'required|integer|exists:competitions,id',
            'questions' => 'required|array',
            'questions.*.question' => 'required|string',
            'questions.*.points' => 'required|integer',
            'questions.*.answers' => 'required|array',
            'questions.*.answers.*' => 'required|string',
            'questions.*.correct' => 'required|integer',
        ];
    }
}
