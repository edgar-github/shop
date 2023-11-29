<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'subscribe_email' => 'required|email|unique:subscriptions,email|max:64',
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'subscribe_email.required' => __('messages.email_required'),
            'subscribe_email.email' => __('messages.email_type'),
            'subscribe_email.unique' => __('messages.email_unique'),
            'subscribe_email.max' => __('messages.email_max'),
        ];
    }
}
