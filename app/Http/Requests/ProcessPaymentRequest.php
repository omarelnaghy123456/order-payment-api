<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:credit_card,paypal',
            'card_number' => 'required_if:payment_method,credit_card',
            'expiry_month' => 'required_if:payment_method,credit_card',
            'expiry_year' => 'required_if:payment_method,credit_card',
            'cvv' => 'required_if:payment_method,credit_card',
            'paypal_email' => 'required_if:payment_method,paypal|email'
        ];
    }
    public function messages(): array
    {
        return [
            'order_id.required' => 'The order ID is required.',
            'order_id.exists' => 'The order ID does not exist.',
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.01.',
            'payment_method.required' => 'The payment method is required.',
            'payment_method.in' => 'The payment method must be one of: credit_card, paypal.',
            'card_number.required_if' => 'The card number is required if payment method is credit card.',
            'expiry_month.required_if' => 'The expiry month is required if payment method is credit card.',
            'expiry_year.required_if' => 'The expiry year is required if payment method is credit card.',
            'cvv.required_if' => 'The CVV is required if payment method is credit card.',
            'paypal_email.required_if' => 'The PayPal email is required if payment method is PayPal.',
            'paypal_email.email' => 'The PayPal email must be a valid email address.',
        ];
    }
}
