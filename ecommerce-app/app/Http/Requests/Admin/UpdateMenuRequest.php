<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Assuming route parameter is 'menu' which is the UUID or ID if implicit binding works.
        // If route binding uses UUID, we need to find the ID or use ignore with UUID column if using Laravel 9+.
        // Assuming standard unique rule 'unique:table,column,except,idColumn'.
        // Since we bound 'menu' to UUID in HasUuid trait, the route parameter will be the model instance if implicit binding works.
        // Wait, FormRequest usually doesn't have the model resolved automatically in rules unless we access route().

        $menuId = $this->route('menu') ? $this->route('menu')->id : null;
        // If route binding is not resolved yet or pass UUID string, we might need to fetch it.
        // However, standard resource controller update method receives $menu model if type hinted.
        // Let's assume $this->route('menu') returns the model or ID.
        // If it returns UUID string (because we didn't use implicit binding in route definition yet which is likely), we need to handle it.
        // But usually unique rule ignores by ID.

        return [
            'key' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('menus')->ignore($this->menu)],
            'name' => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'in:header,footer,sidebar,mobile'],
            'is_active' => ['boolean'],
            'config' => ['nullable', 'array'],
        ];
    }
}
