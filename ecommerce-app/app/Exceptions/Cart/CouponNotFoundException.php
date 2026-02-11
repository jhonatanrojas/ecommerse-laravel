<?php

namespace App\Exceptions\Cart;

class CouponNotFoundException extends CouponException
{
    protected $message = 'Coupon not found.';
}
