<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    // Cache TTL constants in seconds
    const TTL_WEATHER = 3600; // 1 hour
    const TTL_COUNTRY = 604800; // 1 week
    const TTL_HOLIDAYS = 86400; // 1 day

    private function supportsTagging(): bool
    {
        try {
            return method_exists(Cache::getStore(), 'tags');
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function prefixedKey(string $key, string $tag): string
    {
        return "tagged:{$tag}:{$key}";
    }

    public function get(string $key, string $tag)
    {
        if ($this->supportsTagging()) {
            return Cache::tags([$tag])->get($key);
        }
        return Cache::get($this->prefixedKey($key, $tag));
    }

    public function put(string $key, $value, string $tag, ?int $ttl = null)
    {
        $ttl = $ttl ?? $this->getDefaultTtl($tag);
        if ($this->supportsTagging()) {
            Cache::tags([$tag])->put($key, $value, $ttl);
        } else {
            Cache::put($this->prefixedKey($key, $tag), $value, $ttl);
        }
    }

    public function has(string $key, string $tag): bool
    {
        if ($this->supportsTagging()) {
            return Cache::tags([$tag])->has($key);
        }
        return Cache::has($this->prefixedKey($key, $tag));
    }

    public function forget(string $key, string $tag)
    {
        if ($this->supportsTagging()) {
            Cache::tags([$tag])->forget($key);
        } else {
            Cache::forget($this->prefixedKey($key, $tag));
        }
    }

    public function flushTag(string $tag)
    {
        if ($this->supportsTagging()) {
            Cache::tags([$tag])->flush();
        }
    }

    private function getDefaultTtl(string $tag): int
    {
        return match ($tag) {
            'weather' => self::TTL_WEATHER,
            'country' => self::TTL_COUNTRY,
            'holidays' => self::TTL_HOLIDAYS,
            default => 3600,
        };
    }

    public function remember(string $key, string $tag, callable $callback, ?int $ttl = null)
    {
        $ttl = $ttl ?? $this->getDefaultTtl($tag);
        if ($this->supportsTagging()) {
            return Cache::tags([$tag])->remember($key, $ttl, $callback);
        }
        return Cache::remember($this->prefixedKey($key, $tag), $ttl, $callback);
    }
}
