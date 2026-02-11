<?php

namespace App\Exceptions\Cart;

class UnauthorizedCartAccessException extends CartException
{
    protected $message = 'Unauthorized access to cart.';
}
