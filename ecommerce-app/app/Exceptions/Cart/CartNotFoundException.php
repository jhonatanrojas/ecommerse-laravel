<?php

namespace App\Exceptions\Cart;

class CartNotFoundException extends CartException
{
    protected $message = 'Cart not found.';
}
