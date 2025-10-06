<?php

namespace App\Exceptions;

use App\Exceptions\ApiException;

class RateLimitException extends ApiException
{
    public function __construct(string $message = "API rate limit exceeded", ?int $statusCode = 429, \Exception $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
    }
}
