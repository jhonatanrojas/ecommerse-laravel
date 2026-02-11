<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_address_id' => ['nullable', 'integer', 'exists:addresses,id'],
            'billing_address_id' => ['nullable', 'integer', 'exists:addresses,id'],
            'payment_method' => ['required', 'string', 'in:credit_card,debit_card,paypal,bank_transfer,cash_on_delivery'],
            'shipping_method' => ['required', 'string', 'in:standard,express,overnight'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_address_id.exists' => 'La dirección de envío seleccionada no existe.',
            'billing_address_id.exists' => 'La dirección de facturación seleccionada no existe.',
            'payment_method.required' => 'El método de pago es obligatorio.',
            'payment_method.in' => 'El método de pago seleccionado no es válido.',
            'shipping_method.required' => 'El método de envío es obligatorio.',
            'shipping_method.in' => 'El método de envío seleccionado no es válido.',
            'customer_notes.max' => 'Las notas del cliente no pueden exceder 1000 caracteres.',
        ];
    }
}
