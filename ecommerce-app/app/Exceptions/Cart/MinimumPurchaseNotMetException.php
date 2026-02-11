<?php

namespace App\Exceptions\Cart;

class MinimumPurchaseNotMetException extends CouponException
{
    protected $message = 'Minimum purchase amount not met for this coupon.';
}
