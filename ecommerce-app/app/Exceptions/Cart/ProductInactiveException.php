<?php

namespace App\Exceptions\Cart;

class ProductInactiveException extends CartItemException
{
    protected $message = 'Product is not active.';
}
