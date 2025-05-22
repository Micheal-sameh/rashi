<?php

namespace App\Http\Requests;

use App\Enums\RewardStatus;
use Illuminate\Foundation\Http\FormRequest;

class RewardCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'quantity' => 'required|integer|gt:0',
            'points' => 'required|integer|gt:0',
            'status' => 'required|in:'.implode(',', array_column(RewardStatus::all(), 'value')),
        ];
    }
}
