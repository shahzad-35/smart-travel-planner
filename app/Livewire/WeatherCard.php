<?php

namespace App\Livewire;

use App\DTOs\WeatherDTO;
use App\Services\External\WeatherService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class WeatherCard extends Component
{
    public string $location = '';
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

    public function mount(string $location = ''): void
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(3)->get('https://ipwho.is/');
            $data = $response->json();
            $detectedCountry = $data['country'] ?? '';
        } catch (\Throwable $e) {
            $detectedCountry = '';
        }

        $this->location = $location ?: ($this->location ?: $detectedCountry);

        if (Auth::check()) {
            $pref = Auth::user()->preference;
            $unit = $pref?->temperature_unit ?? (Auth::user()->preferences['temperature_unit'] ?? 'C');
            $this->units = $unit === 'F' ? 'imperial' : 'metric';
        }

        $this->loadWeather();
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


