<?php

namespace App\Http\Requests;

use App\Rules\CanBuyRewardRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'reward_id' => 'required|exists:rewards,id',
            'quantity' => ['required', 'integer', 'gt:0', new CanBuyRewardRule($this->reward_id)],
        ];
    }
}
