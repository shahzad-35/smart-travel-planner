<?php

namespace App\Services\External;

use App\Services\BaseService;
use App\Services\CacheService;
use Illuminate\Support\Facades\Http;
use Exception;

/**
 * Resolves a visitor's approximate location (city-level) from their IP
 * address using the free ipwho.is API (no key required). Private and
 * reserved addresses — e.g. 127.0.0.1 during local development — can't be
 * geolocated, so those fall back to the server's egress IP, which on a
 * dev machine is the developer's own network.
 */
class GeoIpService extends BaseService
{
    private const BASE_URL = 'https://ipwho.is/';
    private const CACHE_TAG = 'geoip';
    private const CACHE_TTL = 21600; // 6 hours

    public function __construct(CacheService $cacheService)
    {
        parent::__construct($cacheService);
    }

    /**
     * Locate an IP address. Returns null when the lookup fails.
     *
     * @return array{city: string, country: string, country_code: string, latitude: float|null, longitude: float|null}|null
     */
    public function locate(?string $ip): ?array
    {
        // ipwho.is called without an IP resolves the caller's egress IP.
        $ip = $this->isPublicIp($ip) ? $ip : '';
        $cacheKey = 'geoip_' . ($ip ?: 'self');

        return $this->cacheService->remember($cacheKey, self::CACHE_TAG, function () use ($ip) {
            try {
                $response = Http::timeout(5)->get(self::BASE_URL . $ip);
                $data = $response->json();

                if (!$response->successful() || !is_array($data) || !($data['success'] ?? false)) {
                    $this->logError('GeoIP lookup unsuccessful: ' . $response->body());
                    return null;
                }

                return [
                    'city' => (string) ($data['city'] ?? ''),
                    'country' => (string) ($data['country'] ?? ''),
                    'country_code' => (string) ($data['country_code'] ?? ''),
                    'latitude' => isset($data['latitude']) ? (float) $data['latitude'] : null,
                    'longitude' => isset($data['longitude']) ? (float) $data['longitude'] : null,
                ];
            } catch (Exception $e) {
                $this->logError('GeoIP lookup failed: ' . $e->getMessage());
                return null;
            }
        }, self::CACHE_TTL);
    }

    private function isPublicIp(?string $ip): bool
    {
        return $ip !== null && filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }
}
