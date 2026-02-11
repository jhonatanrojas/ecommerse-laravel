<?php

namespace App\Exceptions\Cart;

class InvalidQuantityException extends CartItemException
{
    protected $message = 'Invalid quantity specified.';
}
