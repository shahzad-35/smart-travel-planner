<?php

namespace App\DTOs;

class WeatherDTO
{
    public string $location;
    public float $temperature;
    public string $condition;
    public int $humidity;
    public float $windSpeed;

    public function __construct(string $location, float $temperature, string $condition, int $humidity, float $windSpeed)
    {
        $this->location = $location;
        $this->temperature = $temperature;
        $this->condition = $condition;
        $this->humidity = $humidity;
        $this->windSpeed = $windSpeed;
    }

    public function toArray(): array
    {
        return [
            'location' => $this->location,
            'temperature' => $this->temperature,
            'condition' => $this->condition,
            'humidity' => $this->humidity,
            'wind_speed' => $this->windSpeed,
        ];
    }
}
