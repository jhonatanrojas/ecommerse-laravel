<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreShippingStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:shipping_statuses,slug'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function validatedPayload(): array
    {
        $validated = $this->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['is_default'] = $validated['is_default'] ?? false;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        return $validated;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'slug.unique' => 'Este slug ya está en uso.',
            'slug.max' => 'El slug no puede exceder 255 caracteres.',
            'color.regex' => 'El color debe ser un código hexadecimal válido (ej: #FF5733).',
            'sort_order.integer' => 'El orden debe ser un número entero.',
            'sort_order.min' => 'El orden debe ser mayor o igual a 0.',
        ];
    }
}
