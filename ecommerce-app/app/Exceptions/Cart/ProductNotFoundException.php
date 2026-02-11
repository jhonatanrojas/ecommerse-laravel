<?php

namespace App\Exceptions\Cart;

class ProductNotFoundException extends CartItemException
{
    protected $message = 'Product not found.';
}
