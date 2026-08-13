<?php

namespace App\Services\External;

use App\DTOs\CountryDTO;
use App\Services\ApiResponseHandler;
use App\Services\BaseService;
use App\Services\CacheService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Exception;

class CountryService extends BaseService
{
    private const OFFLINE_DATA_PATH = 'countries_offline.json';
    private const CACHE_TAG = 'country';
    private const CACHE_TTL = 604800; // 1 week in seconds

    private string $baseUrl;

    private string $apiKey;

    public function __construct(CacheService $cacheService)
    {
        parent::__construct($cacheService);
        $this->baseUrl = config('services.restcountries.base_url', 'https://api.restcountries.com/countries/v5');
        $this->apiKey = config('services.restcountries.api_key', 'rc_live_demo');
    }

    /**
     * Get country info by name or code
     *
     * @param string $nameOrCode
     * @return CountryDTO|null
     */
    public function getCountryInfo(string $nameOrCode): ?CountryDTO
    {
        $cacheKey = "country_info_v5_{$nameOrCode}";

        return $this->cacheService->remember($cacheKey, self::CACHE_TAG, function () use ($nameOrCode) {
            try {
                if ($this->isCountryCode($nameOrCode)) {
                    $codeLength = strlen($nameOrCode);
                    $codeType = $codeLength === 2 ? 'alpha_2' : 'alpha_3';
                    $url = "{$this->baseUrl}/codes.{$codeType}/" . strtoupper($nameOrCode);
                } else {
                    $url = "{$this->baseUrl}/name?" . http_build_query(['q' => $nameOrCode]);
                }

                $handler = new ApiResponseHandler('CountryService');
                $response = $handler->execute(function () use ($url) {
                    return Http::timeout(10)
                        ->withToken($this->apiKey)
                        ->get($url);
                }, ['url' => $url, 'type' => 'getCountryInfo']);

                if ($response->successful()) {
                    $json = $response->json();
                    $objects = $json['data']['objects'] ?? [];
                    if (empty($objects)) {
                        return $this->getOfflineCountryInfo($nameOrCode);
                    }
                    return $this->mapToCountryDTO($objects[0]);
                } else {
                    $this->logError("REST Countries API error: " . $response->body());
                    return $this->getOfflineCountryInfo($nameOrCode);
                }
            } catch (Exception $e) {
                $this->logError("Failed to fetch country info: " . $e->getMessage());
                return $this->getOfflineCountryInfo($nameOrCode);
            }
        }, self::CACHE_TTL);
    }

    /**
     * Search countries by query for autocomplete
     *
     * @param string $query
     * @return CountryDTO[]
     */
    public function searchCountries(string $query): array
    {
        $cacheKey = "country_search_v5_{$query}";

        return $this->cacheService->remember($cacheKey, self::CACHE_TAG, function () use ($query) {
            try {
                $url = "{$this->baseUrl}/name?" . http_build_query(['q' => $query]);

                $handler = new ApiResponseHandler('CountryService');
                $response = $handler->execute(function () use ($url) {
                    return Http::timeout(10)
                        ->withToken($this->apiKey)
                        ->get($url);
                }, ['query' => $query, 'type' => 'searchCountries']);

                if ($response->successful()) {
                    $json = $response->json();
                    $objects = $json['data']['objects'] ?? [];
                    if (empty($objects)) {
                        return [];
                    }
                    $results = [];
                    foreach ($objects as $countryData) {
                        $results[] = $this->mapToCountryDTO($countryData);
                    }
                    return $results;
                } else {
                    $this->logError("REST Countries API search error: " . $response->body());
                    return [];
                }
            } catch (Exception $e) {
                $this->logError("Failed to search countries: " . $e->getMessage());
                return [];
            }
        }, self::CACHE_TTL);
    }

    /**
     * Validate if input is a valid country code (ISO 3166-1 alpha-2 or alpha-3)
     *
     * @param string $code
     * @return bool
     */
    public function isCountryCode(string $code): bool
    {
        return preg_match('/^[A-Za-z]{2,3}$/', $code) === 1;
    }

    /**
     * Map REST Countries API response to CountryDTO
     *
     * @param array $data
     * @return CountryDTO
     */
    private function mapToCountryDTO(array $data): CountryDTO
    {
        // v5 uses 'names' instead of 'name', 'codes' instead of 'cca2'
        $name = $data['names']['common'] ?? ($data['name']['common'] ?? '');
        $code = $data['codes']['alpha_2'] ?? ($data['cca2'] ?? ($data['codes']['alpha_3'] ?? ($data['cca3'] ?? '')));
        $capitalRaw = $data['capitals'][0] ?? $data['capital'][0] ?? '';
        $capital = is_array($capitalRaw) ? ($capitalRaw['name'] ?? (string) reset($capitalRaw)) : (string) $capitalRaw;
        $region = $data['region'] ?? '';
        $population = $data['population'] ?? 0;

        $currency = null;
        if (isset($data['currencies']) && is_array($data['currencies'])) {
            $currencies = array_values($data['currencies']);
            $currency = $currencies[0]['name'] ?? null;
        }

        $languages = [];
        if (isset($data['languages']) && is_array($data['languages'])) {
            $languages = array_values($data['languages']);
        }

        $timezone = isset($data['timezones'][0]) ? $data['timezones'][0] : null;

        // v5 uses 'flag.url_svg' instead of 'flags.svg'
        $flag = $data['flag']['url_svg'] ?? ($data['flags']['svg'] ?? null);

        $latitude = null;
        $longitude = null;
        if (isset($data['latlng']) && is_array($data['latlng']) && count($data['latlng']) >= 2) {
            $latitude = is_numeric($data['latlng'][0]) ? (float)$data['latlng'][0] : null;
            $longitude = is_numeric($data['latlng'][1]) ? (float)$data['latlng'][1] : null;
        }
        
        return new CountryDTO(
            name: $name,
            code: $code,
            capital: $capital,
            region: $region,
            population: $population,
            currency: $currency,
            languages: $languages,
            timezone: $timezone,
            flag: $flag,
            latitude: $latitude,
            longitude: $longitude
        );
    }

    /**
     * Get country info from offline fallback data
     *
     * @param string $nameOrCode
     * @return CountryDTO|null
     */
    private function getOfflineCountryInfo(string $nameOrCode): ?CountryDTO
    {
        try {
            if (!Storage::exists(self::OFFLINE_DATA_PATH)) {
                return null;
            }
            $json = Storage::get(self::OFFLINE_DATA_PATH);
            $countries = json_decode($json, true);
            if (!$countries) {
                return null;
            }
            foreach ($countries as $countryData) {
                if (strcasecmp($countryData['name'], $nameOrCode) === 0 ||
                    strcasecmp($countryData['code'], $nameOrCode) === 0) {
                    return new CountryDTO(
                        name: $countryData['name'],
                        code: $countryData['code'],
                        capital: $countryData['capital'] ?? '',
                        region: $countryData['region'] ?? '',
                        population: $countryData['population'] ?? 0,
                        currency: $countryData['currency'] ?? null,
                        languages: $countryData['languages'] ?? [],
                        timezone: $countryData['timezone'] ?? null,
                        flag: $countryData['flag'] ?? null,
                        latitude: isset($countryData['latitude']) ? (float)$countryData['latitude'] : null,
                        longitude: isset($countryData['longitude']) ? (float)$countryData['longitude'] : null
                    );
                }
            }
            return null;
        } catch (Exception $e) {
            $this->logError("Failed to load offline country data: " . $e->getMessage());
            return null;
        }
    }
}
