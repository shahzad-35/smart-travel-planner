<?php

namespace Tests\Unit;

use App\DTOs\WeatherDTO;
use App\Models\ApiLog;
use App\Services\External\WeatherService;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WeatherServiceTest extends TestCase
{

    private WeatherService $weatherService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the API key config
        config(['services.openweather.api_key' => 'test_api_key']);

        $cacheService = app(CacheService::class);
        $this->weatherService = new WeatherService($cacheService);
    }

    public function testGetCurrentWeatherSuccess()
    {
        $location = 'London';
        $units = 'metric';

        Http::fake([
            'api.openweathermap.org/data/2.5/weather*' => Http::response([
                'main' => ['temp' => 15.5, 'humidity' => 80],
                'weather' => [['description' => 'clear sky']],
                'wind' => ['speed' => 5.5],
            ], 200),
        ]);

        $weather = $this->weatherService->getCurrentWeather($location, $units);

        // Check that the API log entry was created
        $this->assertDatabaseHas('api_logs', [
            'api_name' => 'WeatherService',
        ]);

        $log = ApiLog::where('api_name', 'WeatherService')->first();
        $this->assertNotNull($log->response_data);
        $this->assertIsString($log->response_data);

        $this->assertInstanceOf(WeatherDTO::class, $weather);
        $this->assertEquals($location, $weather->location);
        $this->assertEquals(15.5, $weather->temperature);
        $this->assertEquals('clear sky', $weather->condition);
        $this->assertEquals(80, $weather->humidity);
        $this->assertEquals(5.5, $weather->windSpeed);
    }

    public function testGetCurrentWeatherApiFailure()
    {
        $location = 'Nowhere';
        $units = 'metric';

        Http::fake([
            'api.openweathermap.org/data/2.5/weather*' => Http::response(null, 500),
        ]);

        $weather = $this->weatherService->getCurrentWeather($location, $units);

        $this->assertNull($weather);
    }

    public function testGetForecastSuccess()
    {
        $location = 'London';
        $units = 'metric';

        $forecastData = [
            'list' => [],
        ];

        // Create 7 days of forecast data (one per day)
        for ($i = 0; $i < 7; $i++) {
            $forecastData['list'][] = [
                'dt' => strtotime("+{$i} days"),
                'main' => ['temp' => 10 + $i, 'humidity' => 70 + $i],
                'weather' => [['description' => 'cloudy']],
                'wind' => ['speed' => 3.5 + $i],
            ];
        }

        Http::fake([
            'api.openweathermap.org/data/2.5/forecast*' => Http::response($forecastData, 200),
        ]);

        $forecast = $this->weatherService->getForecast($location, $units);

        $this->assertIsArray($forecast);
        $this->assertCount(7, $forecast);

        foreach ($forecast as $dayWeather) {
            $this->assertInstanceOf(WeatherDTO::class, $dayWeather);
            $this->assertEquals($location, $dayWeather->location);
            $this->assertEquals('cloudy', $dayWeather->condition);
        }
    }

    public function testGetForecastApiFailure()
    {
        $location = 'Nowhere';
        $units = 'metric';

        Http::fake([
            'api.openweathermap.org/data/2.5/forecast*' => Http::response(null, 500),
        ]);

        $forecast = $this->weatherService->getForecast($location, $units);

        $this->assertNull($forecast);
    }
}
