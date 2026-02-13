<?php

namespace App\Http\Requests;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $admin = $this->user('admin');

        return $admin?->can('manage_pages') || $admin?->can('edit_pages') || false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('pages', 'slug')],
            'content' => ['required', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:1000'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'slug.unique' => 'Este slug ya está en uso.',
            'content.required' => 'El contenido es obligatorio.',
            'meta_title.max' => 'El meta title no puede exceder 255 caracteres.',
            'meta_description.max' => 'La meta descripción no puede exceder 500 caracteres.',
            'meta_keywords.max' => 'Las meta keywords no pueden exceder 1000 caracteres.',
        ];
    }

    public function prepareForValidation(): void
    {
        $slug = $this->input('slug');

        $this->merge([
            'slug' => filled($slug) ? Str::slug($slug) : null,
            'is_published' => $this->boolean('is_published'),
            'content' => Page::sanitizeHtml((string) $this->input('content', '')),
        ]);
    }
}
