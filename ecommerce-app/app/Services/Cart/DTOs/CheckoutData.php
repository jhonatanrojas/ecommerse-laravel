<?php

namespace App\Services\Cart\DTOs;

class CheckoutData
{
    public function __construct(
        public readonly ?int $shippingAddressId,
        public readonly ?int $billingAddressId,
        public readonly string $paymentMethod,
        public readonly string $shippingMethod,
        public readonly ?string $customerNotes
    ) {}
}
