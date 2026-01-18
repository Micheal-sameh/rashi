<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuizRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    $competitionId = $this->input('competition_id');
                    if ($competitionId) {
                        $competition = \App\Models\Competition::find($competitionId);
                        if ($competition) {
                            $quizDate = \Carbon\Carbon::parse($value);
                            $startDate = \Carbon\Carbon::parse($competition->start_at);
                            $endDate = \Carbon\Carbon::parse($competition->end_at);

                            if ($quizDate->lt($startDate) || $quizDate->gt($endDate)) {
                                $fail('The quiz date must be between the competition start date ('.$startDate->format('Y-m-d').') and end date ('.$endDate->format('Y-m-d').').');
                            }
                        }
                    }
                },
            ],
            'competition_id' => 'required|integer|exists:competitions,id',
            'questions' => 'required|array',
            'questions.*.question' => 'required|string',
            'questions.*.points' => 'required|integer',
            'questions.*.answers' => 'required|array|min:2|max:4',
            'questions.*.answers.*' => 'required|string',
            'questions.*.correct' => 'required|integer',
        ];
    }
}
