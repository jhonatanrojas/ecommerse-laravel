<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Stripe',
                'slug' => 'stripe',
                'driver_class' => \App\Services\Payments\Drivers\StripePaymentDriver::class,
                'is_active' => true,
                'config' => [
                    'mode' => 'sandbox',
                    'public_key' => '',
                    'secret_key' => '',
                    'webhook_secret' => '',
                ],
            ],
            [
                'name' => 'PayPal',
                'slug' => 'paypal',
                'driver_class' => \App\Services\Payments\Drivers\PayPalPaymentDriver::class,
                'is_active' => true,
                'config' => [
                    'mode' => 'sandbox',
                    'client_id' => '',
                    'client_secret' => '',
                    'webhook_secret' => '',
                ],
            ],
            [
                'name' => 'Mercado Pago',
                'slug' => 'mercadopago',
                'driver_class' => \App\Services\Payments\Drivers\MercadoPagoPaymentDriver::class,
                'is_active' => true,
                'config' => [
                    'mode' => 'sandbox',
                    'access_token' => '',
                    'public_key' => '',
                    'webhook_secret' => '',
                ],
            ],
            [
                'name' => 'Pago en Efectivo',
                'slug' => 'cash',
                'driver_class' => \App\Services\Payments\Drivers\CashPaymentDriver::class,
                'is_active' => true,
                'config' => [
                    'auto_approve' => true,
                    'instructions' => 'Pagar en efectivo al recibir el pedido.',
                ],
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::query()->updateOrCreate(
                ['slug' => $method['slug']],
                $method
            );
        }
    }
}

