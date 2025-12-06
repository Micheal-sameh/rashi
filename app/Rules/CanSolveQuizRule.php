<?php

namespace App\Rules;

use App\Repositories\QuizRepository;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CanSolveQuizRule implements ValidationRule
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

        if (! $quiz?->isSolved?->isEmpty()) {
            $fail(__('messages.you_have_already_solved_this_quiz'));
        }

        if (Carbon::parse($quiz->date)->lt(today())) {
            $fail(__('messages.this_quiz_is_no_longer_available_to_solve'));
        }
    }
}
