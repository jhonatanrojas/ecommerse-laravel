<?php

namespace App\Http\Requests;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'order_id' => ['required', 'string', 'max:64'],
            'payment_method' => [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $exists = PaymentMethod::query()
                        ->where('slug', (string) $value)
                        ->where('is_active', true)
                        ->exists();

                    if (!$exists) {
                        $fail('El método de pago no está disponible.');
                    }
                },
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
