<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'menu_id' => ['required', 'exists:menus,id'],
            'parent_id' => ['nullable', 'exists:menu_items,id'],
            'label' => ['required', 'string', 'max:100'],
            'url' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'in:internal,external,route,category,custom'],
            'target' => ['nullable', 'in:_self,_blank'],
            'order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'open_in_new_tab' => ['boolean'],
            'route_name' => ['required_if:type,route', 'nullable', 'string'],
            'route_params' => ['nullable', 'array'],
            'icon' => ['nullable', 'string', 'max:50'],
            'css_classes' => ['nullable', 'string', 'max:100'],
            'badge_text' => ['nullable', 'string', 'max:50'],
            'badge_color' => ['nullable', 'string', 'max:50'],
        ];
    }
}
