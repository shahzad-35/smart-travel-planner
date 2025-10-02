<?php

namespace App\DTOs;

class UserPreferenceDTO
{
    public ?string $preferredLanguage;
    public ?string $preferredCurrency;
    public ?array $travelInterests;

    public function __construct(?string $preferredLanguage = null, ?string $preferredCurrency = null, ?array $travelInterests = null)
    {
        $this->preferredLanguage = $preferredLanguage;
        $this->preferredCurrency = $preferredCurrency;
        $this->travelInterests = $travelInterests;
    }

    public function toArray(): array
    {
        return [
            'preferred_language' => $this->preferredLanguage,
            'preferred_currency' => $this->preferredCurrency,
            'travel_interests' => $this->travelInterests,
        ];
    }
}
