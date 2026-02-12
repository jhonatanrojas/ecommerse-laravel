<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * SetDefaultAddressRequest validates default address configuration requests.
 * 
 * Validates address_id (uuid, exists) and type (enum) for setting
 * a default shipping or billing address.
 */
class SetDefaultAddressRequest extends FormRequest
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
            'address_id' => 'required|uuid|exists:addresses,uuid',
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
            'address_id.required' => 'El ID de la dirección es requerido.',
            'address_id.uuid' => 'El ID de la dirección debe ser un UUID válido.',
            'address_id.exists' => 'La dirección especificada no existe.',
            'type.required' => 'El tipo de dirección es requerido.',
            'type.in' => 'El tipo de dirección debe ser shipping o billing.',
        ];
    }
}
