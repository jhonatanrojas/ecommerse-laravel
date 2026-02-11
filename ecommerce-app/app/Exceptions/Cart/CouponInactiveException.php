<?php

namespace App\Exceptions\Cart;

class CouponInactiveException extends CouponException
{
    protected $message = 'Coupon is not active.';
}
