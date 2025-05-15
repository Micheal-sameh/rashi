<?php

namespace App\Rules;

use App\Repositories\QuizRepository;
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

        if ($quiz?->isSolved) {
            $fail('You have already solved this quiz');
        }
    }
}
