<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'string', 'max:64'],
            'payment_method' => ['required', 'string', Rule::in(config('payments.methods', []))],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}

