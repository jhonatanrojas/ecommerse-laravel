<?php

namespace App\Exceptions\Cart;

class CartExpiredException extends CartException
{
    protected $message = 'The cart has expired.';
}
