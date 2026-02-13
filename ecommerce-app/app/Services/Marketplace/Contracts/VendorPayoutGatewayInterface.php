<?php

namespace App\Services\Marketplace\Contracts;

use App\Models\VendorPayout;

interface VendorPayoutGatewayInterface
{
    public function payout(VendorPayout $payout): array;
}
