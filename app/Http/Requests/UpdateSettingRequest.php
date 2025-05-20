<?php

namespace App\Http\Requests;

use App\Rules\SettingValidatorRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'settings' => ['required', 'array', new SettingValidatorRule],
        ];
    }
}
