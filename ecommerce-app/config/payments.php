<?php

return [
    'methods' => ['stripe', 'paypal', 'mercadopago'],

    'gateways' => [
        'stripe' => [
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'force_failure' => env('STRIPE_FORCE_FAILURE', false),
        ],
        'paypal' => [
            'webhook_secret' => env('PAYPAL_WEBHOOK_SECRET'),
            'force_failure' => env('PAYPAL_FORCE_FAILURE', false),
        ],
        'mercadopago' => [
            'webhook_secret' => env('MERCADOPAGO_WEBHOOK_SECRET'),
            'force_failure' => env('MERCADOPAGO_FORCE_FAILURE', false),
        ],
    ],
];

