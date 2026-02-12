<?php

namespace App\Exceptions\Payments;

use InvalidArgumentException;

class UnsupportedPaymentGatewayException extends InvalidArgumentException
{
    public static function forMethod(string $method): self
    {
        return new self("Payment gateway not supported for method: {$method}");
    }
}

