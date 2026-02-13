<?php

namespace App\Services\Payments;

use App\Exceptions\Payments\UnsupportedPaymentGatewayException;
use App\Models\PaymentMethod;
use App\Services\Payments\Contracts\PaymentGatewayInterface;

class PaymentGatewayFactory
{
    public function make(string $method, bool $onlyActive = true): PaymentGatewayInterface
    {
        $normalizedMethod = str_replace('_', '', strtolower(trim($method)));

        $query = PaymentMethod::query()->where('slug', $normalizedMethod);
        if ($onlyActive) {
            $query->where('is_active', true);
        }

        $paymentMethod = $query->first();

        if (!$paymentMethod) {
            throw UnsupportedPaymentGatewayException::forMethod($method);
        }

        $driverClass = $paymentMethod->driver_class;
        if (!class_exists($driverClass)) {
            throw UnsupportedPaymentGatewayException::forMethod($method);
        }

        $driver = app()->makeWith($driverClass, [
            'config' => (array) ($paymentMethod->config ?? []),
        ]);

        if (!$driver instanceof PaymentGatewayInterface) {
            throw UnsupportedPaymentGatewayException::forMethod($method);
        }

        return $driver;
    }
}
