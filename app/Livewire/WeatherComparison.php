<?php

namespace App\Livewire;

use App\DTOs\WeatherDTO;
use App\Services\External\WeatherService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WeatherComparison extends Component
{
    /** @var array<int, string> */
    public array $locations = [];
    public string $units = 'metric';
    public string $newLocation = '';
    public bool $isLoading = false;
    /** @var array<string, string> */
    public array $errorsByLocation = [];
    /** @var array<string, array<string, mixed>|null> */
    public array $currentByLocation = [];

    private WeatherService $weatherService;

    public function boot(WeatherService $weatherService): void
    {
        $this->weatherService = $weatherService;
    }

    public function mount(array $locations = []): void
    {
        if (Auth::check()) {
            $prefs = Auth::user()->preferences ?? [];
            $this->units = ($prefs['temperature_unit'] ?? 'metric') === 'imperial' ? 'imperial' : 'metric';
            $saved = $prefs['weather_compare_locations'] ?? null;
            if (is_array($saved) && !empty($saved)) {
                $locations = $saved;
            }
        }

        $this->locations = !empty($locations) ? array_values(array_unique(array_map([$this, 'normalizeLocation'], $locations))) : ['Pakistan', 'Dubai'];
        $this->loadCurrent();
    }

    public function addLocation(string $location): void
    {
        $location = $this->normalizeLocation($location);
        if ($location === '') {
            return;
        }
        if ($this->containsLocation($location)) {
            return;
        }

        $this->locations[] = $location;
        $this->persistLocations();

        try {
            $dto = $this->weatherService->getCurrentWeather($location, $this->units);
            $this->currentByLocation[$location] = $dto ? $dto->toArray() : null;
            unset($this->errorsByLocation[$location]);
        } catch (\Throwable $e) {
            $this->currentByLocation[$location] = null;
            $this->errorsByLocation[$location] = 'Failed to load weather.';
        }
    }

    public function addNewLocation(): void
    {
        $location = $this->newLocation;
        $this->newLocation = '';
        $this->addLocation($location);
    }

    public function removeLocation(string $location): void
    {
        $this->locations = array_values(array_filter($this->locations, fn ($l) => $l !== $location));
        unset($this->currentByLocation[$location]);
        unset($this->errorsByLocation[$location]);
        $this->persistLocations();
    }

    public function loadCurrent(): void
    {
        $this->isLoading = true;
        $this->currentByLocation = [];
        $this->errorsByLocation = [];
        foreach ($this->locations as $loc) {
            try {
                $dto = $this->weatherService->getCurrentWeather($loc, $this->units);
                $this->currentByLocation[$loc] = $dto ? $dto->toArray() : null;
            } catch (\Throwable $e) {
                $this->currentByLocation[$loc] = null;
                $this->errorsByLocation[$loc] = 'Failed to load weather.';
            }
        }
        $this->isLoading = false;
    }

    public function updateUnits(string $unit): void
    {
        $this->units = $unit === 'imperial' ? 'imperial' : 'metric';
        if (Auth::check()) {
            $user = Auth::user();
            $prefs = $user->preferences ?? [];
            $prefs['temperature_unit'] = $this->units;
            $user->preferences = $prefs;
            $user->save();
        }
        $this->loadCurrent();
    }

    private function normalizeLocation(string $location): string
    {
        $location = trim($location);
        if ($location === '') {
            return '';
        }
        return preg_replace('/\s+/', ' ', ucwords(strtolower($location)));
    }

    private function containsLocation(string $location): bool
    {
        $lower = array_map('strtolower', $this->locations);
        return in_array(strtolower($location), $lower, true);
    }

    private function persistLocations(): void
    {
        if (!Auth::check()) {
            return;
        }
        $user = Auth::user();
        $prefs = $user->preferences ?? [];
        $prefs['weather_compare_locations'] = $this->locations;
        $user->preferences = $prefs;
        $user->save();
    }

    public function render()
    {
        return view('livewire.weather-comparison');
    }
}


