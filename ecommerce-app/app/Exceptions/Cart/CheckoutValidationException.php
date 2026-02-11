<?php

namespace App\Exceptions\Cart;

class CheckoutValidationException extends CheckoutException
{
    protected $message = 'Checkout validation failed.';
}
