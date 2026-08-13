<?php

namespace App\Services\External;

use App\DTOs\DestinationDTO;
use App\Models\City;
use App\Services\BaseService;
use App\Services\CacheService;
use Illuminate\Support\Facades\Http;
use Exception;

/**
 * Searches destinations below the country level: states/provinces from a
 * bundled offline dataset, cities via the OpenWeather Geocoding API
 * (which shares the OPENWEATHER_API_KEY already used by WeatherService).
 */
class LocationService extends BaseService
{
    private const GEOCODING_URL = 'https://api.openweathermap.org/geo/1.0/direct';
    private const STATES_DATA_PATH = 'states_offline.json';
    private const CACHE_TAG = 'location';
    private const CACHE_TTL = 86400; // 1 day
    private const MAX_STATE_RESULTS = 6;
    private const MAX_CITY_RESULTS = 5;

    private ?string $apiKey;

    public function __construct(CacheService $cacheService)
    {
        parent::__construct($cacheService);
        $this->apiKey = config('services.openweather.api_key');
    }

    /**
     * Grouped destination search across all levels.
     *
     * @return array{countries: array[], states: array[], cities: array[]}
     */
    public function searchDestinations(string $query, CountryService $countryService): array
    {
        $query = trim($query);

        return [
            'countries' => array_map(
                fn($c) => $c->toArray(),
                $countryService->searchCountries($query)
            ),
            'states' => array_map(fn($s) => $s->toArray(), $this->searchStates($query)),
            'cities' => array_map(fn($c) => $c->toArray(), $this->searchCities($query)),
        ];
    }

    /**
     * Search states/provinces in the bundled offline dataset.
     *
     * @return DestinationDTO[]
     */
    public function searchStates(string $query): array
    {
        $cacheKey = 'location_states_' . mb_strtolower($query);

        return $this->cacheService->remember($cacheKey, self::CACHE_TAG, function () use ($query) {
            $results = [];
            foreach ($this->loadOfflineStates() as $state) {
                if (stripos($state['name'], $query) === false) {
                    continue;
                }
                $results[] = new DestinationDTO(
                    type: $state['type'] ?: 'state',
                    name: $state['name'],
                    countryCode: $state['country_code'],
                    countryName: $state['country_name'],
                    latitude: $state['latitude'],
                    longitude: $state['longitude'],
                    flag: $this->flagUrl($state['country_code'])
                );
                if (count($results) >= self::MAX_STATE_RESULTS) {
                    break;
                }
            }

            return $results;
        }, self::CACHE_TTL);
    }

    /**
     * List the cities of a state/province, most significant first.
     *
     * @return array{cities: array[], total: int}
     */
    public function getCitiesForState(string $countryCode, string $stateName, int $limit = 24): array
    {
        $base = City::query()
            ->where('country_code', $countryCode)
            ->where('state_name', $stateName);

        $total = (clone $base)->count();

        $cities = $base
            ->orderByDesc('population')
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(fn(City $city) => (new DestinationDTO(
                type: 'city',
                name: $city->name,
                countryCode: $city->country_code,
                state: $city->state_name,
                latitude: $city->latitude,
                longitude: $city->longitude,
                flag: $this->flagUrl($city->country_code)
            ))->toArray())
            ->all();

        return ['cities' => $cities, 'total' => $total];
    }

    /**
     * Search cities (capitals included). Uses the local cities table when
     * seeded (population-ranked, works offline); falls back to the
     * OpenWeather Geocoding API otherwise.
     *
     * @return DestinationDTO[]
     */
    public function searchCities(string $query): array
    {
        $local = $this->searchCitiesLocally($query);
        if ($local !== null) {
            return $local;
        }

        if (empty($this->apiKey)) {
            return [];
        }

        $cacheKey = 'location_cities_' . mb_strtolower($query);

        return $this->cacheService->remember($cacheKey, self::CACHE_TAG, function () use ($query) {
            try {
                $response = Http::timeout(10)->get(self::GEOCODING_URL, [
                    'q' => $query,
                    'limit' => self::MAX_CITY_RESULTS,
                    'appid' => $this->apiKey,
                ]);

                if (!$response->successful()) {
                    $this->logError('OpenWeather geocoding error: ' . $response->body());
                    return [];
                }

                $data = $response->json();
                if (!is_array($data)) {
                    return [];
                }

                $results = [];
                foreach ($data as $place) {
                    if (!is_array($place) || empty($place['name']) || empty($place['country'])) {
                        continue;
                    }
                    $results[] = new DestinationDTO(
                        type: 'city',
                        name: $place['name'],
                        countryCode: $place['country'],
                        state: $place['state'] ?? null,
                        latitude: isset($place['lat']) ? (float) $place['lat'] : null,
                        longitude: isset($place['lon']) ? (float) $place['lon'] : null,
                        flag: $this->flagUrl($place['country'])
                    );
                }

                return $results;
            } catch (Exception $e) {
                $this->logError('Failed to search cities: ' . $e->getMessage());
                return [];
            }
        }, self::CACHE_TTL);
    }

    /**
     * Search the seeded cities table (prefix match, population-ranked).
     * Returns null when the table is empty so the caller can fall back
     * to the geocoding API.
     *
     * @return DestinationDTO[]|null
     */
    private function searchCitiesLocally(string $query): ?array
    {
        try {
            if (!City::query()->exists()) {
                return null;
            }

            return City::query()
                ->where('name', 'like', $query . '%')
                ->orderByDesc('population')
                ->orderBy('name')
                ->limit(self::MAX_CITY_RESULTS)
                ->get()
                ->map(fn(City $city) => new DestinationDTO(
                    type: 'city',
                    name: $city->name,
                    countryCode: $city->country_code,
                    state: $city->state_name,
                    latitude: $city->latitude,
                    longitude: $city->longitude,
                    flag: $this->flagUrl($city->country_code)
                ))
                ->all();
        } catch (Exception $e) {
            $this->logError('Local city search failed: ' . $e->getMessage());
            return null;
        }
    }

    private function loadOfflineStates(): array
    {
        try {
            $path = storage_path('app/' . self::STATES_DATA_PATH);
            if (!is_file($path)) {
                return [];
            }
            $states = json_decode((string) file_get_contents($path), true);

            return is_array($states) ? $states : [];
        } catch (Exception $e) {
            $this->logError('Failed to load offline states data: ' . $e->getMessage());
            return [];
        }
    }

    private function flagUrl(string $countryCode): string
    {
        return 'https://flagcdn.com/' . strtolower($countryCode) . '.svg';
    }
}
