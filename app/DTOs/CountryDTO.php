<?php

namespace App\DTOs;

class CountryDTO
{
    public string $name;
    public string $code;
    public string $capital;
    public string $region;
    public int $population;
    public ?string $currency;
    public array $languages;
    public ?string $timezone;
    public ?string $flag;

    public function __construct(
        string $name,
        string $code,
        string $capital,
        string $region,
        int $population,
        ?string $currency = null,
        array $languages = [],
        ?string $timezone = null,
        ?string $flag = null
    ) {
        $this->name = $name;
        $this->code = $code;
        $this->capital = $capital;
        $this->region = $region;
        $this->population = $population;
        $this->currency = $currency;
        $this->languages = $languages;
        $this->timezone = $timezone;
        $this->flag = $flag;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'capital' => $this->capital,
            'region' => $this->region,
            'population' => $this->population,
            'currency' => $this->currency,
            'languages' => $this->languages,
            'timezone' => $this->timezone,
            'flag' => $this->flag,
        ];
    }
}
