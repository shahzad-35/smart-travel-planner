<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Exceptions\RateLimitException;
use App\Models\ApiLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class ApiResponseHandler
{
    private int $maxRetries;
    private int $retryDelay; // milliseconds
    private int $circuitBreakerThreshold;
    private int $circuitBreakerTimeout; // seconds
    private string $circuitBreakerCacheKey;
    private string $apiName;

    public function __construct(string $apiName, int $maxRetries = 3, int $retryDelay = 500, int $circuitBreakerThreshold = 5, int $circuitBreakerTimeout = 60)
    {
        $this->apiName = $apiName;
        $this->maxRetries = $maxRetries;
        $this->retryDelay = $retryDelay;
        $this->circuitBreakerThreshold = $circuitBreakerThreshold;
        $this->circuitBreakerTimeout = $circuitBreakerTimeout;
        $this->circuitBreakerCacheKey = "circuit_breaker_{$apiName}";
    }

    /**
     * Execute an API call with retry, circuit breaker, and error handling
     *
     * @param callable $apiCall A callable that performs the API call and returns the response
     * @param array $requestData Optional request data to log
     * @return mixed The API respons
     * @throws ApiException|RateLimitException
     */
    public function execute(callable $apiCall, array $requestData = [])
    {
        if ($this->isCircuitBreakerOpen()) {
            $this->logError("Circuit breaker is open for API: {$this->apiName}");
            throw new ApiException("Circuit breaker is open for API: {$this->apiName}");
        }

        $attempt = 0;
        $lastException = null;

        while ($attempt <= $this->maxRetries) {
            try {
                //actual API call
                $response = $apiCall();

                if ($this->isRateLimitExceeded($response)) {
                    $this->incrementCircuitBreaker();
                    $this->logError("Rate limit exceeded for API: {$this->apiName}");
                    throw new RateLimitException();
                }

                if ($this->isErrorResponse($response)) {
                    $this->incrementCircuitBreaker();
                    $this->logError("API error for {$this->apiName}: " . $this->getErrorMessage($response));
                    throw new ApiException($this->getErrorMessage($response));
                }

                $this->resetCircuitBreaker();
                $this->logApiCall($requestData, $response);
                return $response;
            } catch (RateLimitException $e) {
                $this->logApiCall($requestData, null);
                throw $e;
            } catch (ApiException $e) {
                $lastException = $e;
                $this->logApiCall($requestData, null);
                if ($attempt < $this->maxRetries) {
                    $this->logInfo("Retrying API call for {$this->apiName}, attempt " . ($attempt + 1));
                    usleep($this->retryDelay * 1000);
                }
                $attempt++;
            } catch (Exception $e) {
                $lastException = new ApiException("Unexpected error: " . $e->getMessage(), null, $e);
                $this->incrementCircuitBreaker();
                $this->logApiCall($requestData, null);
                if ($attempt < $this->maxRetries) {
                    $this->logInfo("Retrying API call for {$this->apiName}, attempt " . ($attempt + 1));
                    usleep($this->retryDelay * 1000);
                }
                $attempt++;
            }
        }

        $this->logError("All retry attempts failed for API: {$this->apiName}");
        throw $lastException ?? new ApiException("All retry attempts failed for API: {$this->apiName}");
    }

    /**
     * Check if circuit breaker is open
     *
     * @return bool
     */
    private function isCircuitBreakerOpen(): bool
    {
        $failures = Cache::get($this->circuitBreakerCacheKey . '_failures', 0);
        $lastFailureTime = Cache::get($this->circuitBreakerCacheKey . '_last_failure', 0);

        if ($failures >= $this->circuitBreakerThreshold) {
            if (time() - $lastFailureTime < $this->circuitBreakerTimeout) {
                return true;
            } else {
                // Half-open: allow one request to test
                Cache::put($this->circuitBreakerCacheKey . '_half_open', true, $this->circuitBreakerTimeout);
            }
        }

        return false;
    }

    /**
     * Increment circuit breaker failure count
     */
    private function incrementCircuitBreaker(): void
    {
        $failures = Cache::get($this->circuitBreakerCacheKey . '_failures', 0) + 1;
        Cache::put($this->circuitBreakerCacheKey . '_failures', $failures, $this->circuitBreakerTimeout);
        Cache::put($this->circuitBreakerCacheKey . '_last_failure', time(), $this->circuitBreakerTimeout);

        if (Cache::get($this->circuitBreakerCacheKey . '_half_open', false)) {
            Cache::forget($this->circuitBreakerCacheKey . '_half_open');
        }
    }

    /**
     * Reset circuit breaker on success
     */
    private function resetCircuitBreaker(): void
    {
        Cache::forget($this->circuitBreakerCacheKey . '_failures');
        Cache::forget($this->circuitBreakerCacheKey . '_last_failure');
        Cache::forget($this->circuitBreakerCacheKey . '_half_open');
    }

    /**
     * Check if response indicates rate limit exceeded
     *
     * @param mixed $response
     * @return bool
     */
    private function isRateLimitExceeded($response): bool
    {
        if (method_exists($response, 'status') && $response->status() === 429) {
            return true;
        }
        return false;
    }

    /**
     * Check if response is an error
     *
     * @param mixed $response
     * @return bool
     */
    private function isErrorResponse($response): bool
    {
        if (method_exists($response, 'successful')) {
            return !$response->successful();
        }
        return false;
    }

    /**
     * Get error message from response
     *
     * @param mixed $response
     * @return string
     */
    private function getErrorMessage($response): string
    {
        if (method_exists($response, 'body')) {
            return $response->body();
        }
        return 'Unknown error';
    }

    /**
     * Log API call
     *
     * @param array $requestData
     * @param mixed $responseData
     */
    private function logApiCall(array $requestData, $responseData = null): void
    {
        ApiLog::create([
            'api_name' => $this->apiName,
            'request_data' => $requestData,
            'response_data' => $responseData ? $responseData->body() : null,
        ]);
    }

    /**
     * Get API usage statistics
     *
     * @return array
     */
    public function getApiUsageStats(): array
    {
        return Cache::get("api_stats_{$this->apiName}", ['total' => 0, 'success' => 0, 'failure' => 0]);
    }

    /**
     * Log an info message.
     *
     * @param string $message
     */
    private function logInfo(string $message): void
    {
        Log::info("[{$this->apiName}] " . $message);
    }

    /**
     * Log an error message.
     *
     * @param string $message
     */
    private function logError(string $message): void
    {
        Log::error("[{$this->apiName}] " . $message);
    }
}
