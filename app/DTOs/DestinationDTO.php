<?php

namespace App\DTOs;

class DestinationDTO
{
    public string $type;
    public string $name;
    public ?string $state;
    public string $countryCode;
    public ?string $countryName;
    public ?float $latitude;
    public ?float $longitude;
    public ?string $flag;

    public function __construct(
        string $type,
        string $name,
        string $countryCode,
        ?string $countryName = null,
        ?string $state = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $flag = null
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->countryCode = $countryCode;
        $this->countryName = $countryName;
        $this->state = $state;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->flag = $flag;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'state' => $this->state,
            'country_code' => $this->countryCode,
            'country_name' => $this->countryName,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'flag' => $this->flag,
        ];
    }
}
