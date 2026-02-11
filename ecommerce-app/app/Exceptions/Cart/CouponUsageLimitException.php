<?php

namespace App\Exceptions\Cart;

class CouponUsageLimitException extends CouponException
{
    protected $message = 'Coupon usage limit has been reached.';
}
