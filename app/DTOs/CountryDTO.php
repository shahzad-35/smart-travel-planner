<?php

namespace App\DTOs;

class CountryDTO
{
    public string $name;
    public string $code;
    public string $capital;
    public string $region;
    public int $population;

    public function __construct(string $name, string $code, string $capital, string $region, int $population)
    {
        $this->name = $name;
        $this->code = $code;
        $this->capital = $capital;
        $this->region = $region;
        $this->population = $population;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'capital' => $this->capital,
            'region' => $this->region,
            'population' => $this->population,
        ];
    }
}
