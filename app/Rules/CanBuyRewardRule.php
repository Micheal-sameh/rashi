<?php

namespace App\Rules;

use App\Repositories\RewardRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CanBuyRewardRule implements ValidationRule
{
    public function __construct(protected int $reward_id) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rewardRepository = app(RewardRepository::class);
        $reward = $rewardRepository->findById($this->reward_id);

        $total_price = $reward->points * $value;
        if (auth()->user()->points < $total_price) {
            $fail(__('messages.points_not_enough'));
        }

        if ($reward->quantity >= $value) {
            $fail(__('messages.quantity_not_enough'));
        }

    }
}
