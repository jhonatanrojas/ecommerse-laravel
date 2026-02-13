<?php

namespace App\Services\Marketplace\Payouts;

use App\Models\VendorPayout;
use App\Services\Marketplace\Contracts\VendorPayoutGatewayInterface;

class PayPalPayoutGateway implements VendorPayoutGatewayInterface
{
    public function payout(VendorPayout $payout): array
    {
        return [
            'success' => false,
            'transaction_reference' => null,
            'meta' => [
                'message' => 'Integración PayPal Payouts pendiente de credenciales.',
            ],
        ];
    }
}
