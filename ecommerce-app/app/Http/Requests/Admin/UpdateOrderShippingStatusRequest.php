<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderShippingStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_status_id' => ['required', 'integer', 'exists:shipping_statuses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_status_id.required' => 'El estatus de envío es obligatorio.',
            'shipping_status_id.integer' => 'El estatus de envío debe ser un número entero.',
            'shipping_status_id.exists' => 'El estatus de envío seleccionado no existe.',
        ];
    }
}
