<?php

namespace App\DTOs;

class WeatherDTO
{
    public string $location;
    public float $temperature;
    public string $condition;
    public int $humidity;
    public float $windSpeed;
    public ?float $feelsLike;
    public ?string $icon;
    public ?string $date; // Y-m-d for daily forecasts
    public ?float $minTemp;
    public ?float $maxTemp;
    /** @var array<int, array{event:string,description?:string,severity?:string,start?:int,end?:int}>|null */
    public ?array $alerts;

    public function __construct(string $location, float $temperature, string $condition, int $humidity, float $windSpeed, ?float $feelsLike = null, ?string $icon = null, ?string $date = null, ?float $minTemp = null, ?float $maxTemp = null, ?array $alerts = null)
    {
        $this->location = $location;
        $this->temperature = $temperature;
        $this->condition = $condition;
        $this->humidity = $humidity;
        $this->windSpeed = $windSpeed;
        $this->feelsLike = $feelsLike;
        $this->icon = $icon;
        $this->date = $date;
        $this->minTemp = $minTemp;
        $this->maxTemp = $maxTemp;
        $this->alerts = $alerts;
    }

    public function toArray(): array
    {
        return [
            'location' => $this->location,
            'temperature' => $this->temperature,
            'condition' => $this->condition,
            'humidity' => $this->humidity,
            'wind_speed' => $this->windSpeed,
            'feels_like' => $this->feelsLike,
            'icon' => $this->icon,
            'date' => $this->date,
            'min_temp' => $this->minTemp,
            'max_temp' => $this->maxTemp,
            'alerts' => $this->alerts,
        ];
    }
}
