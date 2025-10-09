<?php

namespace App\Livewire;

use App\DTOs\HolidayDTO;
use App\Models\Trip;
use App\Services\External\HolidayService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class HolidayList extends Component
{
    public string $code;
    #[Url]
    public ?string $startDate = null;
    #[Url]
    public ?string $endDate = null;
    public ?int $tripId = null;

    /** @var HolidayDTO[] */
    public array $holidays = [];

    private HolidayService $holidayService;

    public function boot(HolidayService $holidayService): void
    {
        $this->holidayService = $holidayService;
    }

    public function mount(): void
    {
        if ($this->tripId) {
            $trip = Trip::query()
                ->where('id', $this->tripId)
                ->where('user_id', Auth::id())
                ->first();
            if ($trip) {
                $this->startDate = $this->startDate ?: optional($trip->start_date)?->format('Y-m-d');
                $this->endDate = $this->endDate ?: optional($trip->end_date)?->format('Y-m-d');
            }
        }
        $this->loadHolidays();
    }

    public function updatedStartDate(): void { $this->loadHolidays(); }
    public function updatedEndDate(): void { $this->loadHolidays(); }

    private function loadHolidays(): void
    {
        if (!$this->code || !$this->startDate || !$this->endDate) {
            $this->holidays = [];
            return;
        }
        $holidays = $this->holidayService->getHolidays($this->code, $this->startDate, $this->endDate);
        $this->holidays = array_map(fn($h) => $h->toArray(), $holidays);
    }

    public function render()
    {
        return view('livewire.holiday-list');
    }
}


