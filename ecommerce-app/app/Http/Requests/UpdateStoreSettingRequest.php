<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateStoreSettingRequest
 * 
 * Cumple con el Principio de Responsabilidad Única (SRP):
 * Solo se encarga de validar los datos de entrada para actualizar ajustes.
 */
class UpdateStoreSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Ajustar según la lógica de permisos
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'store_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'currency' => ['required', 'string', 'max:10'],
            'currency_symbol' => ['required', 'string', 'max:5'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'transactional_email' => ['nullable', 'email', 'max:255'],
            'maintenance_mode' => ['nullable', 'boolean'],
            'allow_guest_checkout' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'store_name' => 'nombre de la tienda',
            'logo' => 'logo',
            'currency' => 'moneda',
            'currency_symbol' => 'símbolo de moneda',
            'tax_rate' => 'tasa de impuesto',
            'support_email' => 'email de soporte',
            'transactional_email' => 'email transaccional',
            'maintenance_mode' => 'modo de mantenimiento',
            'allow_guest_checkout' => 'permitir checkout de invitados',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'store_name.required' => 'El :attribute es obligatorio.',
            'logo.image' => 'El :attribute debe ser una imagen.',
            'logo.mimes' => 'El :attribute debe ser un archivo de tipo: jpeg, png, jpg, gif, svg.',
            'logo.max' => 'El :attribute no debe ser mayor a 2MB.',
            'currency.required' => 'La :attribute es obligatoria.',
            'currency_symbol.required' => 'El :attribute es obligatorio.',
            'tax_rate.required' => 'La :attribute es obligatoria.',
            'tax_rate.numeric' => 'La :attribute debe ser un número.',
            'tax_rate.min' => 'La :attribute debe ser al menos :min.',
            'tax_rate.max' => 'La :attribute no debe ser mayor a :max.',
            'support_email.email' => 'El :attribute debe ser una dirección de correo válida.',
            'transactional_email.email' => 'El :attribute debe ser una dirección de correo válida.',
        ];
    }
}
