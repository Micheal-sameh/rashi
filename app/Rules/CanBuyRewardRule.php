<?php

namespace App\Rules;

use App\Repositories\RewardRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Cache;

class CanBuyRewardRule implements ValidationRule
{
    public function __construct(protected int $reward_id) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rewardRepository = app(RewardRepository::class);
        $reward = $rewardRepository->findById($this->reward_id);

        $user = Cache::get('auth_user_'.auth()->id()) ?? auth()->user();
        $total_price = $reward->points * $value;
        if ($user->points < $total_price) {
            $fail(__('messages.points_not_enough'));
        }

        if ($reward->quantity < $value) {
            $fail(__('messages.quantity_not_enough'));
        }

    }
}
