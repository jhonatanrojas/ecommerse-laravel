<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:120', 'alpha_dash', Rule::unique('order_statuses', 'slug')],
            'color' => ['nullable', 'string', 'regex:/^#?[A-Fa-f0-9]{6}$/'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }

    public function validatedPayload(): array
    {
        $data = $this->validated();
        $data['slug'] = strtolower((string) $data['slug']);
        $data['color'] = !empty($data['color'])
            ? '#' . ltrim((string) $data['color'], '#')
            : null;
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['is_default'] = (bool) ($data['is_default'] ?? false);

        return $data;
    }
}
