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

    public function __construct(CacheService $cacheService)
    {
        parent::__construct($cacheService);
        $this->baseUrl = config('services.restcountries.base_url', 'https://restcountries.com/v3.1');
    }

    /**
     * Get country info by name or code
     *
     * @param string $nameOrCode
     * @return CountryDTO|null
     */
    public function getCountryInfo(string $nameOrCode): ?CountryDTO
    {
        $cacheKey = "country_info_{$nameOrCode}";

        return $this->cacheService->remember($cacheKey, self::CACHE_TAG, function () use ($nameOrCode) {
            try {
                $endpoint = $this->isCountryCode($nameOrCode) ? "alpha/{$nameOrCode}" : "name/{$nameOrCode}";

                // Use centralized handler so API calls are logged
                $handler = new ApiResponseHandler('CountryService');
                $response = $handler->execute(function () use ($endpoint) {
                    return Http::timeout(10)->get("{$this->baseUrl}/{$endpoint}");
                }, ['endpoint' => $endpoint, 'type' => 'getCountryInfo']);

                if ($response->successful()) {
                    $data = $response->json();
                    if (empty($data)) {
                        return $this->getOfflineCountryInfo($nameOrCode);
                    }
                    // REST Countries API returns array for name search, object for alpha code
                    $countryData = is_array($data) ? $data[0] : $data;
                    return $this->mapToCountryDTO($countryData);
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
        $cacheKey = "country_search_{$query}";

        return $this->cacheService->remember($cacheKey, self::CACHE_TAG, function () use ($query) {
            try {
                // Use centralized handler so API calls are logged
                $handler = new ApiResponseHandler('CountryService');
                $response = $handler->execute(function () use ($query) {
                    return Http::timeout(10)->get("{$this->baseUrl}/name/{$query}");
                }, ['query' => $query, 'type' => 'searchCountries']);

                if ($response->successful()) {
                    $data = $response->json();
                    if (empty($data)) {
                        return [];
                    }
                    $results = [];
                    foreach ($data as $countryData) {
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
        $name = $data['name']['common'] ?? '';
        $code = $data['cca2'] ?? ($data['cca3'] ?? '');
        $capital = isset($data['capital'][0]) ? $data['capital'][0] : '';
        $region = $data['region'] ?? '';
        $population = $data['population'] ?? 0;

        // Currency: get first currency name
        $currency = null;
        if (isset($data['currencies']) && is_array($data['currencies'])) {
            $currencies = array_values($data['currencies']);
            $currency = $currencies[0]['name'] ?? null;
        }

        // Languages: get all language names
        $languages = [];
        if (isset($data['languages']) && is_array($data['languages'])) {
            $languages = array_values($data['languages']);
        }

        // Timezones: get first timezone
        $timezone = isset($data['timezones'][0]) ? $data['timezones'][0] : null;

        // Flag: get svg flag url
        $flag = $data['flags']['svg'] ?? null;

        // Coordinates: latlng array [lat, lng]
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
