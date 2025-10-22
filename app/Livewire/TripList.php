<?php

namespace App\Livewire;

use App\Repositories\TripRepository;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TripList extends Component
{
    use WithPagination;

    public string $viewMode = 'card'; // card or list


    public string $statusFilter = '';
    public string $typeFilter = '';
    public string $destinationFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';


    public string $sortBy = 'start_date';
    public string $sortDirection = 'desc';


    public string $search = '';

    public int $perPage = 10;

    public array $statusOptions = [
        '' => 'All Statuses',
        'planned' => 'Planned',
        'ongoing' => 'Ongoing',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public array $typeOptions = [
        '' => 'All Types',
        'business' => 'Business',
        'leisure' => 'Leisure',
        'adventure' => 'Adventure',
        'family' => 'Family',
        'solo' => 'Solo',
    ];

    public array $sortOptions = [
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'destination' => 'Destination',
        'budget' => 'Budget',
        'type' => 'Type',
        'status' => 'Status',
        'created_at' => 'Created Date',
    ];

    private TripRepository $tripRepository;

    public function boot(TripRepository $tripRepository)
    {
        $this->tripRepository = $tripRepository;
    }


    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'card' ? 'list' : 'card';
    }


    public function applyFilters()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->statusFilter = '';
        $this->typeFilter = '';
        $this->destinationFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->search = '';
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy == $field) {
            $this->sortDirection = $this->sortDirection == 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    public function getTripsProperty()
    {
        $filters = [
            'status' => $this->statusFilter,
            'type' => $this->typeFilter,
            'destination' => $this->destinationFilter,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
        ];

        return $this->tripRepository->getFilteredTrips(
            Auth::id(),
            $filters,
            $this->sortBy,
            $this->sortDirection,
            $this->search,
            $this->perPage
        );
    }

    public function mount()
    {
        $this->clearFilters();
    }
    
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedDestinationFilter()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.trip-list', [
            'trips' => $this->trips,
        ]);
    }
}
