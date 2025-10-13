<?php

namespace App\Livewire;

use App\Models\Trip;
use App\Services\External\WeatherService;
use App\Services\External\HolidayService;
use App\Repositories\TripRepository;
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

    protected $listeners = ['refreshTripDetails' => '$refresh'];

    public function mount($id)
    {
        $this->trip = Trip::with(['packingItems', 'expenses', 'notes', 'user'])
                         ->where('user_id', Auth::id())
                         ->findOrFail($id);

        $this->loadTripData();
    }

    public function loadTripData()
    {
        // Load weather forecast
        $weatherService = app(WeatherService::class);
        $this->weatherForecast = $weatherService->getForecast($this->trip->destination) ?? [];

        // Load holidays
        $holidayService = app(HolidayService::class);
        $this->holidays = $holidayService->getHolidays(
            $this->trip->country_code,
            $this->trip->start_date->format('Y-m-d'),
            $this->trip->end_date->format('Y-m-d')
        );

        // Calculate packing progress
        $this->calculatePackingProgress();

        // Calculate expenses
        $this->calculateExpenses();

        // Load current note
        $this->noteContent = $this->trip->notes()->first()?->note ?? '';
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
        $this->trip->notes()->delete(); // Remove existing notes
        if (!empty($this->noteContent)) {
            $this->trip->notes()->create(['note' => $this->noteContent]);
        }
        $this->editingNote = false;
        $this->dispatch('refreshTripDetails');
    }

    public function cancelEditNote()
    {
        $this->noteContent = $this->trip->notes()->first()?->note ?? '';
        $this->editingNote = false;
    }

    public function deleteTrip()
    {
        $this->trip->delete();
        return redirect()->route('trips')->with('success', 'Trip deleted successfully.');
    }

    public function shareTrip()
    {
        // TODO: Implement trip sharing functionality
        $this->dispatch('show-toast', ['message' => 'Trip sharing coming soon!', 'type' => 'info']);
    }

    public function exportTrip()
    {
        // TODO: Implement trip export functionality
        $this->dispatch('show-toast', ['message' => 'Trip export coming soon!', 'type' => 'info']);
    }

    public function getWeatherForDate($date)
    {
        return collect($this->weatherForecast)->firstWhere('date', $date);
    }

    public function getHolidaysForDate($date)
    {
        return collect($this->holidays)->where('date', $date)->all();
    }

    public function render()
    {
        return view('livewire.trip-details');
    }
}
