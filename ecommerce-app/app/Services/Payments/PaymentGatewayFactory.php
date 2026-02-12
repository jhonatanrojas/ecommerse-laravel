<?php

namespace App\Services\Payments;

use App\Exceptions\Payments\UnsupportedPaymentGatewayException;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use App\Services\Payments\Drivers\MercadoPagoPaymentDriver;
use App\Services\Payments\Drivers\PayPalPaymentDriver;
use App\Services\Payments\Drivers\StripePaymentDriver;

class PaymentGatewayFactory
{
    public function __construct(
        private StripePaymentDriver $stripeDriver,
        private PayPalPaymentDriver $paypalDriver,
        private MercadoPagoPaymentDriver $mercadoPagoDriver
    ) {}

    public function make(string $method): PaymentGatewayInterface
    {
        $normalizedMethod = strtolower(trim($method));

        return match ($normalizedMethod) {
            'stripe' => $this->stripeDriver,
            'paypal' => $this->paypalDriver,
            'mercadopago', 'mercado_pago' => $this->mercadoPagoDriver,
            default => throw UnsupportedPaymentGatewayException::forMethod($method),
        };
    }
}

