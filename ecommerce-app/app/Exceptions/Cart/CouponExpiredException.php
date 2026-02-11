<?php

namespace App\Exceptions\Cart;

class CouponExpiredException extends CouponException
{
    protected $message = 'Coupon has expired.';
}
