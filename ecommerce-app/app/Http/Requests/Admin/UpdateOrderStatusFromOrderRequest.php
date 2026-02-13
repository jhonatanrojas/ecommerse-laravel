<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusFromOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'order_status_id' => [
                'required',
                'integer',
                Rule::exists('order_statuses', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
