<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($productId)
            ],
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($productId)
            ],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0', 'gt:price'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'array'],
            'dimensions.length' => ['nullable', 'numeric', 'min:0'],
            'dimensions.width' => ['nullable', 'numeric', 'min:0'],
            'dimensions.height' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'sku.required' => 'El SKU es obligatorio.',
            'sku.unique' => 'Este SKU ya está en uso.',
            'price.required' => 'El precio es obligatorio.',
            'price.min' => 'El precio debe ser mayor o igual a 0.',
            'compare_price.gt' => 'El precio de comparación debe ser mayor al precio regular.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.min' => 'El stock debe ser mayor o igual a 0.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'images.max' => 'Puedes subir máximo 5 imágenes.',
            'images.*.image' => 'El archivo debe ser una imagen.',
            'images.*.mimes' => 'Las imágenes deben ser de tipo: jpeg, jpg, png, webp.',
            'images.*.max' => 'Cada imagen no debe superar los 2MB.',
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge(['is_active' => $this->boolean('is_active')]);
        }

        if ($this->has('is_featured')) {
            $this->merge(['is_featured' => $this->boolean('is_featured')]);
        }

        // Preparar dimensiones como array
        if ($this->has('dimensions.length') || $this->has('dimensions.width') || $this->has('dimensions.height')) {
            $dimensions = [
                'length' => $this->input('dimensions.length'),
                'width' => $this->input('dimensions.width'),
                'height' => $this->input('dimensions.height'),
            ];
            $this->merge(['dimensions' => $dimensions]);
        }
    }
}
