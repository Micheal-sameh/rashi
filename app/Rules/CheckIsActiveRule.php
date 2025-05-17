<?php

namespace App\Rules;

use App\Enums\CompetitionStatus;
use App\Models\Competition;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckIsActiveRule implements ValidationRule
{
    public function __construct(protected object $model) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $object = $this->model->find($value);

        $is_active = match (get_class($object)) {
            Competition::class => $object->status == CompetitionStatus::ACTIVE,
            Quiz::class => $object->competition->status == CompetitionStatus::ACTIVE,
            QuizQuestion::class => $object->quiz->competition->status == CompetitionStatus::ACTIVE,
            default => false,
        };
        if ($is_active) {
            $fail('is active cannot be deleted');
        }
    }
}
