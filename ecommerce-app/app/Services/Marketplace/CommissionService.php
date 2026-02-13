<?php

namespace App\Services\Marketplace;

use App\Models\OrderItem;
use App\Models\StoreSetting;
use App\Models\Vendor;

class CommissionService
{
    /**
     * Priority: category commission > vendor commission > global commission.
     */
    public function calculateForItem(OrderItem $item, Vendor $vendor): array
    {
        $globalRate = (float) (StoreSetting::query()->value('marketplace_commission_rate') ?? 10);
        $categoryRate = $item->product?->category?->commission_rate;
        $vendorRate = $vendor->commission_rate;

        $rate = (float) ($categoryRate ?? $vendorRate ?? $globalRate);
        $subtotal = (float) $item->subtotal;
        $commission = round(($subtotal * $rate) / 100, 2);
        $earnings = round($subtotal - $commission, 2);

        return [
            'rate' => $rate,
            'subtotal' => $subtotal,
            'commission_amount' => $commission,
            'vendor_earnings' => $earnings,
        ];
    }
}
