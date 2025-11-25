<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaderBoardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
        ];
    }
}
