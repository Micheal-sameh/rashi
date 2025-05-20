<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersFilterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'string',
            'group_id' => 'integer',
            'sort_by' => 'in:points,score',
            'direction' => 'in:asc,desc',
        ];
    }
}
