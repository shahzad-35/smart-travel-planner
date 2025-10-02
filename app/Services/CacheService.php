<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    // Cache TTL constants in seconds
    const TTL_WEATHER = 3600; // 1 hour
    const TTL_COUNTRY = 604800; // 1 week
    const TTL_HOLIDAYS = 86400; // 1 day

    /**
     * Get cached data with tagging.
     *
     * @param string $key
     * @param string $tag
     * @return mixed
     */
    public function get(string $key, string $tag)
    {
        return Cache::tags([$tag])->get($key);
    }

    /**
     * Put data in cache with tagging and TTL.
     *
     * @param string $key
     * @param mixed $value
     * @param string $tag
     * @param int|null $ttl
     */
    public function put(string $key, $value, string $tag, ?int $ttl = null)
    {
        $ttl = $ttl ?? $this->getDefaultTtl($tag);
        Cache::tags([$tag])->put($key, $value, $ttl);
    }

    /**
     * Check if key exists in cache with tag.
     *
     * @param string $key
     * @param string $tag
     * @return bool
     */
    public function has(string $key, string $tag): bool
    {
        return Cache::tags([$tag])->has($key);
    }

    /**
     * Forget a specific key with tag.
     *
     * @param string $key
     * @param string $tag
     */
    public function forget(string $key, string $tag)
    {
        Cache::tags([$tag])->forget($key);
    }

    /**
     * Flush all cache for a specific tag.
     *
     * @param string $tag
     */
    public function flushTag(string $tag)
    {
        Cache::tags([$tag])->flush();
    }

    /**
     * Get default TTL based on tag.
     *
     * @param string $tag
     * @return int
     */
    private function getDefaultTtl(string $tag): int
    {
        return match ($tag) {
            'weather' => self::TTL_WEATHER,
            'country' => self::TTL_COUNTRY,
            'holidays' => self::TTL_HOLIDAYS,
            default => 3600, // default 1 hour
        };
    }

    /**
     * Remember and cache data if not exists.
     *
     * @param string $key
     * @param string $tag
     * @param callable $callback
     * @param int|null $ttl
     * @return mixed
     */
    public function remember(string $key, string $tag, callable $callback, ?int $ttl = null)
    {
        $ttl = $ttl ?? $this->getDefaultTtl($tag);
        return Cache::tags([$tag])->remember($key, $ttl, $callback);
    }
}
