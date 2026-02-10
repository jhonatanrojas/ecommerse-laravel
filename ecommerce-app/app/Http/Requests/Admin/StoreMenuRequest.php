<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:menus,key'],
            'name' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'in:header,footer,sidebar,mobile'],
            'is_active' => ['boolean'],
            'config' => ['nullable', 'array'],
        ];
    }
}
