<?php

namespace AppyPay\Exceptions;

use RuntimeException;

class AppyPayException extends RuntimeException
{
    public function __construct(string $message, private readonly ?int $status = null)
    {
        parent::__construct($message, $status ?? 0);
    }

    public function status(): ?int
    {
        return $this->status;
    }
}
