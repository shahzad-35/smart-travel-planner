<?php

namespace App\Livewire;

use App\DTOs\CountryDTO;
use App\Models\Trip;
use App\Services\External\CountryService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class CountryInfo extends Component
{
    #[Url]
    public string $code = ''; // ISO code or name

    public ?int $tripId = null;

    public ?array $country = null;

    public ?string $tab = 'info';

    public ?string $startDate = null;
    public ?string $endDate = null;

    private CountryService $countryService;

    public function boot(CountryService $countryService): void
    {
        $this->countryService = $countryService;
    }

    public function mount(): void
    {
        $this->tab = $this->tab ?: 'info';

        if ($this->tripId) {
            $trip = Trip::query()
                ->where('id', $this->tripId)
                ->where('user_id', Auth::id())
                ->first();
            if ($trip) {
                $this->startDate = optional($trip->start_date)?->format('Y-m-d');
                $this->endDate = optional($trip->end_date)?->format('Y-m-d');
            }
        }

        if (!$this->startDate || !$this->endDate) {
            $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            $this->endDate = Carbon::now()->endOfYear()->format('Y-m-d');
        }

        $this->loadCountry();
    }

    public function updatedCode(): void
    {
        $this->loadCountry();
    }

    private function loadCountry(): void
    {
        if (!$this->code) {
            $this->country = null;
            return;
        }
        $country = $this->countryService->getCountryInfo($this->code);
        $this->country = $country?->toArray();
    }

    public function render()
    {
        return view('livewire.country-info');
    }
}


