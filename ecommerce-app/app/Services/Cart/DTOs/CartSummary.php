<?php

namespace App\Services\Cart\DTOs;

class CartSummary
{
    public function __construct(
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly float $tax,
        public readonly float $shippingCost,
        public readonly float $total,
        public readonly int $itemCount,
        public readonly ?string $couponCode
    ) {}
}
