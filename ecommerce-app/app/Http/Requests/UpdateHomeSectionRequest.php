<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeSectionRequest extends FormRequest
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
            'type' => 'required|in:hero,featured_products,featured_categories,banners,testimonials,html_block',
            'title' => 'required|string|max:255',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
            'configuration' => 'required|array',
        ];
    }
}
