<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    protected array $shortages;

    public function __construct(string $message = "Stok bahan baku tidak mencukupi.", array $shortages = [], int $code = 422)
    {
        parent::__construct($message, $code);
        $this->shortages = $shortages;
    }

    public function getShortages(): array
    {
        return $this->shortages;
    }
}
