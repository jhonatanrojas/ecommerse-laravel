<?php

namespace App\Http\Requests\Admin;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateOrderRequest
 * 
 * SOLID: Single Responsibility Principle (SRP)
 * Responsabilidad única: validación de datos para actualización de órdenes
 */
class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // La autorización se maneja en el Policy
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => [
                'sometimes',
                'required',
                Rule::enum(OrderStatus::class),
            ],
            'payment_method' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
            'shipping_method' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
            'notes' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'status' => 'estado',
            'payment_method' => 'método de pago',
            'shipping_method' => 'método de envío',
            'notes' => 'notas',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'El :attribute es obligatorio.',
            'status.enum' => 'El :attribute seleccionado no es válido.',
            'payment_method.max' => 'El :attribute no puede exceder :max caracteres.',
            'shipping_method.max' => 'El :attribute no puede exceder :max caracteres.',
            'notes.max' => 'Las :attribute no pueden exceder :max caracteres.',
        ];
    }
}
