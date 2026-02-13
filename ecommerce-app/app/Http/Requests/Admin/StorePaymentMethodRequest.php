<?php

namespace App\Http\Requests\Admin;

use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:120', Rule::unique('payment_methods', 'slug')],
            'driver_class' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (!is_string($value) || !class_exists($value)) {
                        $fail('La clase del driver no existe.');
                        return;
                    }

                    if (!in_array(PaymentGatewayInterface::class, class_implements($value), true)) {
                        $fail('La clase del driver debe implementar PaymentGatewayInterface.');
                    }
                },
            ],
            'is_active' => ['nullable', 'boolean'],
            'config_json' => ['nullable', 'string', 'json'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedPayload(): array
    {
        $data = $this->validated();
        $data['slug'] = Str::slug((string) ($data['slug'] ?: $data['name']));
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['config'] = !empty($data['config_json'])
            ? (json_decode((string) $data['config_json'], true) ?: [])
            : [];
        unset($data['config_json']);

        return $data;
    }
}
