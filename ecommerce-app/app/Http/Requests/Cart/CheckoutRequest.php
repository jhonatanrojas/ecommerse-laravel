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
            // Shipping Address (can be ID or full address object)
            'shipping_address_id' => ['nullable', 'integer', 'exists:addresses,id'],
            'shipping_address' => ['required_without:shipping_address_id', 'array'],
            'shipping_address.full_name' => ['required_with:shipping_address', 'string', 'max:255'],
            'shipping_address.address_line_1' => ['required_with:shipping_address', 'string', 'max:255'],
            'shipping_address.address_line_2' => ['nullable', 'string', 'max:255'],
            'shipping_address.city' => ['required_with:shipping_address', 'string', 'max:100'],
            'shipping_address.state' => ['required_with:shipping_address', 'string', 'max:100'],
            'shipping_address.postal_code' => ['required_with:shipping_address', 'string', 'max:20'],
            'shipping_address.country' => ['required_with:shipping_address', 'string', 'max:2'],

            // Billing Address (can be ID or full address object)
            'billing_address_id' => ['nullable', 'integer', 'exists:addresses,id'],
            'billing_address' => ['required_without:billing_address_id', 'array'],
            'billing_address.full_name' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.address_line_1' => ['required_with:billing_address', 'string', 'max:255'],
            'billing_address.address_line_2' => ['nullable', 'string', 'max:255'],
            'billing_address.city' => ['required_with:billing_address', 'string', 'max:100'],
            'billing_address.state' => ['required_with:billing_address', 'string', 'max:100'],
            'billing_address.postal_code' => ['required_with:billing_address', 'string', 'max:20'],
            'billing_address.country' => ['required_with:billing_address', 'string', 'max:2'],

            // Payment and Shipping Methods
            'payment_method' => ['required', 'string', 'in:credit_card,debit_card,paypal,bank_transfer,cash_on_delivery'],
            'shipping_method' => ['required', 'string', 'in:standard,express,priority'],
            
            // Notes
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_address_id.exists' => 'La dirección de envío seleccionada no existe.',
            'billing_address_id.exists' => 'La dirección de facturación seleccionada no existe.',
            
            'shipping_address.required_without' => 'Debe proporcionar una dirección de envío.',
            'shipping_address.full_name.required_with' => 'El nombre completo es obligatorio.',
            'shipping_address.address_line_1.required_with' => 'La dirección es obligatoria.',
            'shipping_address.city.required_with' => 'La ciudad es obligatoria.',
            'shipping_address.state.required_with' => 'El estado/provincia es obligatorio.',
            'shipping_address.postal_code.required_with' => 'El código postal es obligatorio.',
            'shipping_address.country.required_with' => 'El país es obligatorio.',
            
            'billing_address.required_without' => 'Debe proporcionar una dirección de facturación.',
            'billing_address.full_name.required_with' => 'El nombre completo es obligatorio.',
            'billing_address.address_line_1.required_with' => 'La dirección es obligatoria.',
            'billing_address.city.required_with' => 'La ciudad es obligatoria.',
            'billing_address.state.required_with' => 'El estado/provincia es obligatorio.',
            'billing_address.postal_code.required_with' => 'El código postal es obligatorio.',
            'billing_address.country.required_with' => 'El país es obligatorio.',
            
            'payment_method.required' => 'El método de pago es obligatorio.',
            'payment_method.in' => 'El método de pago seleccionado no es válido.',
            'shipping_method.required' => 'El método de envío es obligatorio.',
            'shipping_method.in' => 'El método de envío seleccionado no es válido.',
            'notes.max' => 'Las notas no pueden exceder 1000 caracteres.',
        ];
    }
}
