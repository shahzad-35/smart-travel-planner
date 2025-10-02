<?php

namespace App\Services;

use App\Services\CacheService;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Log an info message.
     *
     * @param string $message
     */
    protected function logInfo(string $message): void
    {
        Log::info($message);
    }

    /**
     * Log an error message.
     *
     * @param string $message
     */
    protected function logError(string $message): void
    {
        Log::error($message);
    }
}
