<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
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
            'code' => 'required|string|max:20|unique:coupons',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'type' => 'required|in:single,each_books',
            'all_products' => 'required_without_all:book_id,accessor_id',
        ];
    }
}
