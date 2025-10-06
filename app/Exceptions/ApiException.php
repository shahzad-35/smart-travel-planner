<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected ?int $statusCode;

    public function __construct(string $message = "", ?int $statusCode = null, Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }
}
