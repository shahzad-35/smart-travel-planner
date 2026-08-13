<?php

namespace App\Livewire;

use App\Models\Trip;
use App\Services\External\WeatherService;
use App\Services\External\HolidayService;
use App\Services\ShareTokenService;
use App\Repositories\TripRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TripDetails extends Component
{
    public Trip $trip;
    public $weatherForecast = [];
    public $holidays = [];
    public $packingProgress = 0;
    public $totalExpenses = 0;
    public $budgetUsed = 0;
    public $editingNote = false;
    public $noteContent = '';
    public ?string $shareUrl = null;

    protected $listeners = ['refreshTripDetails' => '$refresh'];

    public function mount($id)
    {
        $this->trip = Trip::with(['packingItems', 'expenses', 'tripNotes', 'user'])
                         ->where('user_id', Auth::id())
                         ->findOrFail($id);

        $this->loadTripData();
    }

    public function loadTripData()
    {
        $weatherService = app(WeatherService::class);
        $pref = Auth::user()->preference ?? null;
        $unit = $pref?->temperature_unit ?? 'C';
        $units = $unit === 'F' ? 'imperial' : 'metric';
        $this->weatherForecast = $weatherService->getForecast($this->trip->destination, $units) ?? [];

        $holidayService = app(HolidayService::class);
        $holidays = $holidayService->getHolidays(
            $this->trip->country_code,
            $this->trip->start_date->format('Y-m-d'),
            $this->trip->end_date->format('Y-m-d')
        );
        $this->holidays = array_map(fn($holiday) => $holiday->toArray(), $holidays);

        $this->calculatePackingProgress();

        $this->calculateExpenses();

        $this->noteContent = $this->trip->tripNotes->first()?->note ?? '';
    }

    public function calculatePackingProgress()
    {
        $packingItems = $this->trip->packingItems;
        if ($packingItems->isEmpty()) {
            $this->packingProgress = 0;
            return;
        }

        $packedCount = $packingItems->where('is_packed', true)->count();
        $this->packingProgress = round(($packedCount / $packingItems->count()) * 100);
    }

    public function calculateExpenses()
    {
        $expenses = $this->trip->expenses;
        $this->totalExpenses = $expenses->sum('amount');

        if ($this->trip->budget > 0) {
            $this->budgetUsed = round(($this->totalExpenses / $this->trip->budget) * 100, 1);
        }
    }

    public function editNote()
    {
        $this->editingNote = true;
    }

    public function saveNote()
    {
        $this->trip->tripNotes()->delete();
        if (!empty($this->noteContent)) {
            $this->trip->tripNotes()->create(['note' => $this->noteContent]);
        }
        $this->editingNote = false;
        $this->dispatch('refreshTripDetails');
    }

    public function cancelEditNote()
    {
        $this->noteContent = $this->trip->tripNotes()->first()?->note ?? '';
        $this->editingNote = false;
    }

    public function deleteTrip()
    {
        $this->trip->delete();
        session()->flash('success', 'Trip deleted successfully.');
        return $this->redirectRoute('trips.listing');
    }

    /**
     * Generate a public, read-only share link for this trip (expires in 7 days).
     */
    public function shareTrip(ShareTokenService $shareTokenService)
    {
        $token = $shareTokenService->generateToken($this->trip);
        $this->shareUrl = route('trips.shared', ['token' => $token]);
    }

    public function hideShareUrl()
    {
        $this->shareUrl = null;
    }

    /**
     * Download a complete PDF report of the trip: overview, notes, expenses,
     * packing checklist, weather forecast, holidays, and status history.
     */
    public function exportTrip()
    {
        $this->trip->loadMissing(['expenses', 'packingItems', 'tripNotes', 'statusHistories']);

        $pdf = Pdf::loadView('pdf.trip-summary', [
            'trip' => $this->trip,
            'weatherForecast' => $this->weatherForecast,
            'holidays' => $this->holidays,
            'packingProgress' => $this->packingProgress,
            'totalExpenses' => $this->totalExpenses,
            'budgetUsed' => $this->budgetUsed,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'trip-' . str_replace(' ', '-', strtolower($this->trip->destination)) . '.pdf');
    }

    public function getWeatherForDate($date)
    {
        return collect($this->weatherForecast)->firstWhere('date', $date);
    }

    public function getHolidaysForDate($date)
    {
        return collect($this->holidays)->where('date', $date)->toArray();
    }

    public function render()
    {
        return view('livewire.trip-details');
    }
}
