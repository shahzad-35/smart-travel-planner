<?php

namespace App\Services\External;

use App\DTOs\WeatherDTO;
use App\Services\BaseService;
use App\Services\CacheService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class WeatherService extends BaseService
{
    private const BASE_URL = 'https://api.openweathermap.org/data/2.5';
    private const FORECAST_URL = 'https://api.openweathermap.org/data/2.5/forecast';
    private const ONECALL_URL = 'https://api.openweathermap.org/data/3.0/onecall';
    private const RATE_LIMIT_KEY = 'weather_api_rate_limit';
    private const RATE_LIMIT_MAX_REQUESTS = 1000; // per hour
    private const RATE_LIMIT_WINDOW = 3600; // 1 hour in seconds

    private string $apiKey;

    public function __construct(CacheService $cacheService)
    {
        parent::__construct($cacheService);
        $this->apiKey = config('services.openweather.api_key');

        if (!$this->apiKey) {
            throw new Exception('OpenWeatherMap API key not configured');
        }
    }

    /**
     * Get current weather for a location
     *
     * @param string $location
     * @param string $units 'metric' or 'imperial'
     * @return WeatherDTO|null
     */
    public function getCurrentWeather(string $location, string $units = 'metric'): ?WeatherDTO
    {
        $cacheKey = "weather_current_{$location}_{$units}";

        $handler = new \App\Services\ApiResponseHandler('WeatherService');

        return $this->cacheService->remember($cacheKey, 'weather', function () use ($location, $units, $handler) {
            try {
                $response = $handler->execute(function () use ($location, $units) {
                    return \Illuminate\Support\Facades\Http::timeout(10)->get(self::BASE_URL . '/weather', [
                        'q' => $location,
                        'appid' => $this->apiKey,
                        'units' => $units,
                    ]);
                }, ['location' => $location, 'units' => $units]);

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->mapToWeatherDTO($data, $location);
                } else {
                    $this->logError('OpenWeatherMap API error: ' . $response->body());
                    return null;
                }
            } catch (\App\Exceptions\ApiException $e) {
                $this->logError("API Exception: " . $e->getMessage());
                return null;
            } catch (Exception $e) {
                $this->logError('Failed to fetch current weather: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Get 7-day weather forecast for a location
     *
     * @param string $location
     * @param string $units 'metric' or 'imperial'
     * @return array|null Array of weather data arrays
     */
    public function getForecast(string $location, string $units = 'metric'): ?array
    {
        $cacheKey = "weather_forecast_{$location}_{$units}";

        return $this->cacheService->remember($cacheKey, 'weather', function () use ($location, $units) {
            if (!$this->checkRateLimit()) {
                $this->logError('Rate limit exceeded for OpenWeatherMap API');
                return null;
            }

            try {
                // First resolve to coordinates for more accurate One Call data
                $geoResponse = Http::timeout(10)->get(self::BASE_URL . '/weather', [
                    'q' => $location,
                    'appid' => $this->apiKey,
                    'units' => $units,
                ]);

                if (!$geoResponse->successful()) {
                    $this->logError('OpenWeatherMap geocode error: ' . $geoResponse->body());
                    return null;
                }

                $geo = $geoResponse->json();
                $lat = $geo['coord']['lat'] ?? null;
                $lon = $geo['coord']['lon'] ?? null;
                if ($lat === null || $lon === null) {
                    $this->logError('Missing coordinates from geocode response');
                    return null;
                }

                $oneCall = Http::timeout(10)->get(self::ONECALL_URL, [
                    'lat' => $lat,
                    'lon' => $lon,
                    'appid' => $this->apiKey,
                    'units' => $units,
                    'exclude' => 'minutely,hourly',
                ]);

                if ($oneCall->successful()) {
                    $data = $oneCall->json();
                    $dtos = $this->mapOneCallToForecastDTOs($data, $location);
                    return array_map(fn($dto) => $dto->toArray(), $dtos);
                } else {
                    $this->logError('OpenWeatherMap One Call API error: ' . $oneCall->body());
                    // Fallback to 5-day/3-hour forecast to approximate next days
                    $fallback = Http::timeout(10)->get(self::FORECAST_URL, [
                        'q' => $location,
                        'appid' => $this->apiKey,
                        'units' => $units,
                    ]);
                    if ($fallback->successful()) {
                        $data = $fallback->json();
                        $dtos = $this->mapForecastToWeatherDTOs($data, $location);
                        return array_map(fn($dto) => $dto->toArray(), $dtos);
                    }
                    return null;
                }
            } catch (Exception $e) {
                $this->logError('Failed to fetch weather forecast: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Convert temperature from Celsius to Fahrenheit
     *
     * @param float $celsius
     * @return float
     */
    public static function celsiusToFahrenheit(float $celsius): float
    {
        return round(($celsius * 9/5) + 32, 1);
    }

    /**
     * Convert temperature from Fahrenheit to Celsius
     *
     * @param float $fahrenheit
     * @return float
     */
    public static function fahrenheitToCelsius(float $fahrenheit): float
    {
        return round(($fahrenheit - 32) * 5/9, 1);
    }

    /**
     * Check if rate limit is exceeded
     *
     * @return bool
     */
    private function checkRateLimit(): bool
    {
        $requests = Cache::get(self::RATE_LIMIT_KEY, 0);

        if ($requests >= self::RATE_LIMIT_MAX_REQUESTS) {
            return false;
        }

        Cache::put(self::RATE_LIMIT_KEY, $requests + 1, self::RATE_LIMIT_WINDOW);
        return true;
    }

    /**
     * Map OpenWeatherMap current weather response to WeatherDTO
     *
     * @param array $data
     * @param string $location
     * @return WeatherDTO
     */
    private function mapToWeatherDTO(array $data, string $location): WeatherDTO
    {
        return new WeatherDTO(
            location: $location,
            temperature: $data['main']['temp'] ?? 0,
            condition: $data['weather'][0]['description'] ?? 'Unknown',
            humidity: $data['main']['humidity'] ?? 0,
            windSpeed: $data['wind']['speed'] ?? 0,
            feelsLike: $data['main']['feels_like'] ?? null,
            icon: $data['weather'][0]['icon'] ?? null,
            date: isset($data['dt']) ? date('Y-m-d', $data['dt']) : null,
            minTemp: $data['main']['temp_min'] ?? null,
            maxTemp: $data['main']['temp_max'] ?? null,
            alerts: null
        );
    }

    /**
     * Map OpenWeatherMap forecast response to array of WeatherDTOs
     *
     * @param array $data
     * @param string $location
     * @return array
     */
    private function mapForecastToWeatherDTOs(array $data, string $location): array
    {
        $forecasts = [];
        $dailyForecasts = [];

        // Group by date and take the first forecast for each day
        foreach ($data['list'] ?? [] as $item) {
            $date = date('Y-m-d', $item['dt']);
            if (!isset($dailyForecasts[$date])) {
                $dailyForecasts[$date] = $item;
            }
        }

        // Take only 7 days
        $dailyForecasts = array_slice($dailyForecasts, 0, 7, true);

        foreach ($dailyForecasts as $date => $item) {
            $forecasts[] = new WeatherDTO(
                location: $location,
                temperature: $item['main']['temp'] ?? 0,
                condition: $item['weather'][0]['description'] ?? 'Unknown',
                humidity: $item['main']['humidity'] ?? 0,
                windSpeed: $item['wind']['speed'] ?? 0,
                feelsLike: $item['main']['feels_like'] ?? null,
                icon: $item['weather'][0]['icon'] ?? null,
                date: $date,
                minTemp: $item['main']['temp_min'] ?? null,
                maxTemp: $item['main']['temp_max'] ?? null,
                alerts: null
            );
        }

        return $forecasts;
    }

    /**
     * Map OpenWeather One Call daily forecast + alerts
     */
    private function mapOneCallToForecastDTOs(array $data, string $location): array
    {
        $alerts = [];
        foreach ($data['alerts'] ?? [] as $alert) {
            $alerts[] = [
                'event' => $alert['event'] ?? 'Alert',
                'description' => $alert['description'] ?? null,
                'severity' => $alert['tags'][0] ?? null,
                'start' => $alert['start'] ?? null,
                'end' => $alert['end'] ?? null,
            ];
        }

        $result = [];
        foreach (array_slice($data['daily'] ?? [], 0, 7) as $day) {
            $result[] = new WeatherDTO(
                location: $location,
                temperature: $day['temp']['day'] ?? 0,
                condition: $day['weather'][0]['description'] ?? 'Unknown',
                humidity: $day['humidity'] ?? 0,
                windSpeed: $day['wind_speed'] ?? 0.0,
                feelsLike: is_array($day['feels_like'] ?? null) ? ($day['feels_like']['day'] ?? null) : ($day['feels_like'] ?? null),
                icon: $day['weather'][0]['icon'] ?? null,
                date: isset($day['dt']) ? date('Y-m-d', $day['dt']) : null,
                minTemp: $day['temp']['min'] ?? null,
                maxTemp: $day['temp']['max'] ?? null,
                alerts: $alerts ?: null
            );
        }

        return $result;
    }
}
