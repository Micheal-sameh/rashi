<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'target_type' => 'required|in:user,group',
            'user_id' => 'nullable|required_if:target_type,user|integer|exists:users,id',
            'group_id' => 'nullable|required_if:target_type,group|integer|exists:groups,id',
            'message' => 'required|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'target_type.required' => 'Please select whether to send to a user or group.',
            'target_type.in' => 'Invalid target type selected.',
            'user_id.required_if' => 'Please select a user to send the notification to.',
            'user_id.exists' => 'The selected user does not exist.',
            'group_id.required_if' => 'Please select a group to send the notification to.',
            'group_id.exists' => 'The selected group does not exist.',
            'message.required' => 'Please enter a notification message.',
            'message.max' => 'The message cannot exceed 1000 characters.',
        ];
    }
}
