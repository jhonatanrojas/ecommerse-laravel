<?php

namespace App\Http\Requests\Admin;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class FilterOrderRequest
 * 
 * SOLID: Single Responsibility Principle (SRP)
 * Responsabilidad única: validación de filtros de búsqueda de órdenes
 */
class FilterOrderRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'status' => [
                'sometimes',
                'nullable',
                Rule::enum(OrderStatus::class),
            ],
            'start_date' => [
                'sometimes',
                'nullable',
                'date',
                'before_or_equal:end_date',
            ],
            'end_date' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'customer' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
            'order_number' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],
            'per_page' => [
                'sometimes',
                'nullable',
                'integer',
                'min:5',
                'max:100',
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
            'start_date' => 'fecha de inicio',
            'end_date' => 'fecha de fin',
            'customer' => 'cliente',
            'order_number' => 'número de orden',
            'per_page' => 'resultados por página',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.enum' => 'El :attribute seleccionado no es válido.',
            'start_date.date' => 'La :attribute debe ser una fecha válida.',
            'start_date.before_or_equal' => 'La :attribute debe ser anterior o igual a la fecha de fin.',
            'end_date.date' => 'La :attribute debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La :attribute debe ser posterior o igual a la fecha de inicio.',
            'customer.max' => 'El :attribute no puede exceder :max caracteres.',
            'order_number.max' => 'El :attribute no puede exceder :max caracteres.',
            'per_page.integer' => 'Los :attribute deben ser un número entero.',
            'per_page.min' => 'Los :attribute deben ser al menos :min.',
            'per_page.max' => 'Los :attribute no pueden exceder :max.',
        ];
    }

    /**
     * Get validated filters
     */
    public function getFilters(): array
    {
        return $this->only([
            'status',
            'start_date',
            'end_date',
            'customer',
            'order_number',
        ]);
    }
}
