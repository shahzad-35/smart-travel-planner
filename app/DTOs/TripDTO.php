<?php

namespace App\DTOs;

use Carbon\Carbon;

class TripDTO
{
    public int $id;
    public string $name;
    public string $destination;
    public Carbon $startDate;
    public Carbon $endDate;
    public string $status;

    public function __construct(int $id, string $name, string $destination, Carbon $startDate, Carbon $endDate, string $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->destination = $destination;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'destination' => $this->destination,
            'start_date' => $this->startDate->toDateString(),
            'end_date' => $this->endDate->toDateString(),
            'status' => $this->status,
        ];
    }
}
