<?php

namespace App\Rules;

use App\Enums\CompetitionStatus;
use App\Repositories\QuizRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckIsActiveRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $quizRepository = app(QuizRepository::class);
        $quiz = $quizRepository->findById($value);
        if (! $quiz || $quiz->competition->status == CompetitionStatus::ACTIVE) {
            $fail('Quiz is active cannot be deleted');
        }
    }
}
