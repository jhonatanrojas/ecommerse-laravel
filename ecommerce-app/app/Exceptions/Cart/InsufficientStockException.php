<?php

namespace App\Exceptions\Cart;

class InsufficientStockException extends CartItemException
{
    protected $message = 'Insufficient stock available.';
}
