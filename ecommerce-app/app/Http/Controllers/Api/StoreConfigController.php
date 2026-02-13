<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\StoreSetting;
use Illuminate\Http\JsonResponse;

class StoreConfigController extends Controller
{
    /**
     * Get public store configuration
     */
    public function index(): JsonResponse
    {
        $settings = StoreSetting::first();

        return response()->json([
            'success' => true,
            'data' => [
                'allow_guest_checkout' => $settings?->allow_guest_checkout ?? false,
                'store_name' => $settings?->store_name ?? config('app.name'),
                'currency' => $settings?->currency ?? 'EUR',
                'currency_symbol' => $settings?->currency_symbol ?? '€',
                'payment_methods' => PaymentMethod::query()
                    ->active()
                    ->orderBy('name')
                    ->get(['name', 'slug'])
                    ->map(fn (PaymentMethod $method) => [
                        'id' => $method->slug,
                        'name' => $method->name,
                        'description' => 'Método habilitado por administración',
                        'icon' => $this->resolvePaymentIcon($method->slug),
                    ])
                    ->values(),
            ],
        ]);
    }

    private function resolvePaymentIcon(string $slug): string
    {
        return match ($slug) {
            'stripe' => 'credit-card',
            'paypal' => 'paypal',
            'mercadopago' => 'wallet',
            'cash' => 'cash',
            default => 'wallet',
        };
    }
}
