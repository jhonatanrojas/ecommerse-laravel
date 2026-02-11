<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            ],
        ]);
    }
}
