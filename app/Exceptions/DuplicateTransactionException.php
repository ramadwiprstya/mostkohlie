<?php

namespace App\Exceptions;

use Exception;

class DuplicateTransactionException extends Exception
{
    public function __construct(string $message = "Transaksi ini telah diproses sebelumnya untuk mencegah pengurangan ganda.", int $code = 409)
    {
        parent::__construct($message, $code);
    }
}
