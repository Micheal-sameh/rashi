<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCompetitionRequest extends FormRequest
{
    public function rules(): array
    {
        $isUpdate = $this->route('id') !== null;

        return [
            'name' => 'required|string',
            'start_at' => ($isUpdate ? '' : 'required').'|date|after_or_equal:today',
            'end_at' => ($isUpdate ? '' : 'required').'|date|after_or_equal:start_at',
            'image' => 'image|mimes:png,jpg'.(! $isUpdate ? '|required' : ''),
            'groups' => 'required|array|min:1',
            'groups.*' => 'required|integer|exists:groups,id',
        ];
    }
}
