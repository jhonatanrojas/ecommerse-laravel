<?php

namespace App\Services\Cart\DTOs;

class ValidationResult
{
    public function __construct(
        public readonly bool $isValid,
        public readonly ?string $errorMessage = null
    ) {}

    public static function success(): self
    {
        return new self(true);
    }

    public static function fail(string $message): self
    {
        return new self(false, $message);
    }
}
