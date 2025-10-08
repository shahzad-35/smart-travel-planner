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
        $this->location = $location ?: ($this->location ?: 'Pakistan');

        if (Auth::check()) {
            $prefs = Auth::user()->preferences ?? [];
            $this->units = ($prefs['temperature_unit'] ?? 'metric') === 'imperial' ? 'imperial' : 'metric';
        }

        $this->loadWeather();
    }

    #[On('weather-unit-changed')]
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

        $this->loadWeather();
    }

    public function loadWeather(): void
    {
        if (trim($this->location) === '') {
            $this->current = null;
            $this->forecast = null;
            return;
        }

        $current = $this->weatherService->getCurrentWeather($this->location, $this->units);
        $this->current = $current ? $current->toArray() : null;

        $forecast = $this->weatherService->getForecast($this->location, $this->units) ?? [];
        $this->forecast = array_map(fn(WeatherDTO $d) => $d->toArray(), $forecast);
    }

    public function render()
    {
        return view('livewire.weather-card');
    }
}


