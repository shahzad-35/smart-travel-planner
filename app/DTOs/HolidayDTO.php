<?php

namespace App\DTOs;

class HolidayDTO
{
    public string $date;
    public string $name;
    public string $type;
    public ?string $description;

    public function __construct(string $date, string $name, string $type, ?string $description = null)
    {
        $this->date = $date;
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
        ];
    }
}
