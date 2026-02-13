<?php

namespace App\Services\Marketplace\Payouts;

use App\Models\VendorPayout;
use App\Services\Marketplace\Contracts\VendorPayoutGatewayInterface;

class ManualPayoutGateway implements VendorPayoutGatewayInterface
{
    public function payout(VendorPayout $payout): array
    {
        return [
            'success' => true,
            'transaction_reference' => 'MANUAL-' . now()->format('YmdHis') . '-' . $payout->id,
            'meta' => [
                'message' => 'Payout registrado manualmente',
            ],
        ];
    }
}
