<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Por defecto se usa el guard "customer" para la web (ecommerce: login,
    | registro, checkout). El guard "admin" se usa explícitamente en rutas
    | /admin/*. La API usa Sanctum para clientes (tokens).
    |
    */

    'defaults' => [
        'guard' => 'customer',
        'passwords' => 'customers',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | - admin: Solo panel administrativo (/admin/*). Sesión. Usuarios con roles
    |   Spatie (guard admin). Login en /admin/login.
    | - customer: Ecommerce web (login/registro en /login, /register). Sesión.
    |   Mismo modelo User; clientes no usan roles del guard admin.
    | - sanctum: API (login/register vía AuthController). Tokens para SPA/API.
    |   Se usa auth:sanctum en rutas api; el usuario se resuelve por token.
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'customers',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        'customer' => [
            'driver' => 'session',
            'provider' => 'customers',
        ],

        'vendor' => [
            'driver' => 'session',
            'provider' => 'vendors',
        ],

        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => 'customers',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | admins y customers usan el mismo modelo User y la misma tabla users.
    | La separación es por contexto: admin = usuarios con roles (guard admin),
    | customer = usuarios del ecommerce (login/registro/checkout).
    |
    */

    'providers' => [
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'customers' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'vendors' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'users' => [
            'provider' => 'customers',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'admins' => [
            'provider' => 'admins',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'customers' => [
            'provider' => 'customers',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'vendors' => [
            'provider' => 'vendors',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => 10800,

];
