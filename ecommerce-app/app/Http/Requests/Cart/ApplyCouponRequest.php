<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class ApplyCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'coupon_code' => ['required', 'string', 'max:50', 'exists:coupons,code'],
        ];
    }

    public function messages(): array
    {
        return [
            'coupon_code.required' => 'El código del cupón es obligatorio.',
            'coupon_code.exists' => 'El código del cupón no es válido.',
        ];
    }
}
