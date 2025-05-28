<?php

namespace App\Rules;

use App\Enums\OrderStatus;
use App\Repositories\OrderRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckCanDeleteOrderRule implements ValidationRule
{
    public function __construct() {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $orderRepository = app(OrderRepository::class);
        $order = $orderRepository->findById($value);

        if ($order->status == OrderStatus::COMPLETED) {
            $fail('is active cannot be deleted');
        }
    }
}
