<?php

namespace App\Livewire;

use App\DTOs\WeatherDTO;
use App\Services\External\GeoIpService;
use App\Services\External\WeatherService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class WeatherCard extends Component
{
    public string $location = '';
    public string $locationLabel = '';
    public bool $autoDetected = false;
    public string $query = '';
    public string $units = 'metric'; // 'metric' or 'imperial'
    /** @var array<string, mixed>|null */
    public ?array $current = null;
    /** @var array<int, array<string, mixed>> */
    public array $forecast = [];

    private WeatherService $weatherService;

    public function boot(WeatherService $weatherService): void
    {
        $this->weatherService = $weatherService;
    }

    public function mount(GeoIpService $geoIpService, string $location = ''): void
    {
        $this->location = $location ?: $this->location;

        if ($this->location === '') {
            $this->detectLocation($geoIpService);
        }

        if (Auth::check()) {
            $pref = Auth::user()->preference;
            $unit = $pref?->temperature_unit ?? (Auth::user()->preferences['temperature_unit'] ?? 'C');
            $this->units = $unit === 'F' ? 'imperial' : 'metric';
        }

        $this->loadWeather();
    }

    /**
     * Manual city search from the page's search box.
     */
    public function search(): void
    {
        $query = trim($this->query);
        if ($query === '') {
            return;
        }

        $this->location = $query;
        $this->locationLabel = '';
        $this->autoDetected = false;
        $this->loadWeather();
    }

    /**
     * Switch back to the visitor's auto-detected location.
     */
    public function useMyLocation(GeoIpService $geoIpService): void
    {
        $this->query = '';
        $this->location = '';
        $this->locationLabel = '';
        $this->autoDetected = false;
        $this->detectLocation($geoIpService);
        $this->loadWeather();
    }

    private function detectLocation(GeoIpService $geoIpService): void
    {
        $place = $geoIpService->locate(request()->ip());

        if (!empty($place['city'])) {
            // Query as "City,CC" so OpenWeather resolves the right city
            // among same-named ones; keep a friendlier label for display.
            $this->location = $place['city']
                . (!empty($place['country_code']) ? ',' . $place['country_code'] : '');
            $this->locationLabel = $place['city']
                . (!empty($place['country']) ? ', ' . $place['country'] : '');
            $this->autoDetected = true;
        } elseif (!empty($place['country'])) {
            $this->location = $place['country'];
            $this->locationLabel = $place['country'];
            $this->autoDetected = true;
        }
    }

    #[On('weather-unit-changed')]
    public function updateUnits(string $unit): void
    {
        $this->units = $unit === 'imperial' ? 'imperial' : 'metric';

        if (Auth::check()) {
            $unitValue = $this->units === 'imperial' ? 'F' : 'C';
            Auth::user()->preference()->updateOrCreate(
                ['user_id' => Auth::id()],
                ['temperature_unit' => $unitValue]
            );
        }

        $this->loadWeather();
    }

    public function loadWeather(): void
    {
        if (trim($this->location) === '') {
            $this->current = null;
            $this->forecast = [];
            return;
        }

        $current = $this->weatherService->getCurrentWeather($this->location, $this->units);
        $this->current = $current ? $current->toArray() : null;

        $forecast = $this->weatherService->getForecast($this->location, $this->units) ?? [];
        $this->forecast = array_map(fn($d) => $d, $forecast);
    }

    public function render()
    {
        return view('livewire.weather-card');
    }
}


