<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($categoryId)
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                Rule::notIn([$categoryId]) // No puede ser su propio padre
            ],
            'image' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'slug.unique' => 'Este slug ya está en uso.',
            'description.max' => 'La descripción no puede exceder 1000 caracteres.',
            'parent_id.exists' => 'La categoría padre seleccionada no existe.',
            'parent_id.not_in' => 'Una categoría no puede ser su propio padre.',
            'order.integer' => 'El orden debe ser un número entero.',
            'order.min' => 'El orden debe ser mayor o igual a 0.',
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => $this->boolean('is_active'),
            ]);
        }
    }
}
