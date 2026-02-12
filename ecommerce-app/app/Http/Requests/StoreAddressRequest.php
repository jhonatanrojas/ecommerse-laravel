<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreAddressRequest validates address creation requests.
 * 
 * Validates all required and optional fields for creating a new address,
 * including type enum validation and phone format validation.
 */
class StoreAddressRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:2',
            'type' => 'required|in:shipping,billing',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'El nombre es requerido.',
            'first_name.string' => 'El nombre debe ser una cadena de texto.',
            'first_name.max' => 'El nombre no puede exceder 255 caracteres.',
            'last_name.required' => 'El apellido es requerido.',
            'last_name.string' => 'El apellido debe ser una cadena de texto.',
            'last_name.max' => 'El apellido no puede exceder 255 caracteres.',
            'company.string' => 'La empresa debe ser una cadena de texto.',
            'company.max' => 'La empresa no puede exceder 255 caracteres.',
            'phone.string' => 'El teléfono debe ser una cadena de texto.',
            'phone.max' => 'El teléfono no puede exceder 20 caracteres.',
            'address_line1.required' => 'La dirección línea 1 es requerida.',
            'address_line1.string' => 'La dirección línea 1 debe ser una cadena de texto.',
            'address_line1.max' => 'La dirección línea 1 no puede exceder 255 caracteres.',
            'address_line2.string' => 'La dirección línea 2 debe ser una cadena de texto.',
            'address_line2.max' => 'La dirección línea 2 no puede exceder 255 caracteres.',
            'city.required' => 'La ciudad es requerida.',
            'city.string' => 'La ciudad debe ser una cadena de texto.',
            'city.max' => 'La ciudad no puede exceder 255 caracteres.',
            'state.required' => 'El estado/provincia es requerido.',
            'state.string' => 'El estado/provincia debe ser una cadena de texto.',
            'state.max' => 'El estado/provincia no puede exceder 255 caracteres.',
            'postal_code.required' => 'El código postal es requerido.',
            'postal_code.string' => 'El código postal debe ser una cadena de texto.',
            'postal_code.max' => 'El código postal no puede exceder 20 caracteres.',
            'country.required' => 'El país es requerido.',
            'country.string' => 'El país debe ser una cadena de texto.',
            'country.max' => 'El país debe ser un código de 2 caracteres.',
            'type.required' => 'El tipo de dirección es requerido.',
            'type.in' => 'El tipo de dirección debe ser shipping o billing.',
        ];
    }
}
