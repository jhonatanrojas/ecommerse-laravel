<?php

namespace App\Services\Marketplace;

use App\Models\Order;
use App\Models\Vendor;
use App\Models\VendorOrder;

class VendorOrderService
{
    public function __construct(
        protected CommissionService $commissionService
    ) {}

    public function syncFromOrder(Order $order): void
    {
        $order->loadMissing([
            'items.product.category',
            'items.vendor',
        ]);

        $grouped = $order->items
            ->filter(fn ($item) => !empty($item->vendor_id))
            ->groupBy('vendor_id');

        foreach ($grouped as $vendorId => $items) {
            $vendor = Vendor::query()->find($vendorId);
            if (! $vendor) {
                continue;
            }

            $subtotal = 0;
            $commissionAmount = 0;
            $earnings = 0;

            foreach ($items as $item) {
                $calc = $this->commissionService->calculateForItem($item, $vendor);
                $subtotal += $calc['subtotal'];
                $commissionAmount += $calc['commission_amount'];
                $earnings += $calc['vendor_earnings'];
            }

            VendorOrder::query()->updateOrCreate(
                [
                    'vendor_id' => $vendor->id,
                    'order_id' => $order->id,
                ],
                [
                    'subtotal' => round($subtotal, 2),
                    'commission_amount' => round($commissionAmount, 2),
                    'vendor_earnings' => round($earnings, 2),
                    'payout_status' => 'pending',
                    'shipping_status' => 'pending',
                    'shipping_method' => $order->shipping_method,
                ]
            );
        }
    }
}
