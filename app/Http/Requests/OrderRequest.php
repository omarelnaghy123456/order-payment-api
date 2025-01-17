<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends BaseFormRequest
{
    const STATUS_OPTIONS = ['pending', 'confirmed', 'cancelled'];
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', 'in:' . implode(',', self::STATUS_OPTIONS)],
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.product_name' => ['sometimes', 'string', 'max:255'],
            'items.*.quantity' => ['sometimes', 'integer', 'min:1'],
            'items.*.price' => ['sometimes', 'numeric', 'min:0.01'],
            'total_amount' => ['sometimes', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'At least one item is required.',
            'items.*.product_name.required' => 'The product name is required.',
            'items.*.quantity.required' => 'The quantity is required.',
            'items.*.price.required' => 'The price is required.',
            'total_amount.required' => 'The total amount is required.',
            'total_amount.sometimes' => 'The total amount must be provided when necessary.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be one of: pending, confirmed, cancelled.',
        ];
    }
}
