<?php

namespace App\Livewire;

use App\Repositories\TripRepository;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TripDashboard extends Component
{
    private TripRepository $tripRepository;

    public function boot(TripRepository $tripRepository)
    {
        $this->tripRepository = $tripRepository;
    }

    /**
     * Get trip statistics
     */
    public function getStatsProperty()
    {
        return $this->tripRepository->getTripStats(Auth::id());
    }

    /**
     * Get upcoming trips for timeline
     */
    public function getUpcomingTripsProperty()
    {
        return $this->tripRepository->getUpcomingTripsForTimeline(Auth::id(), limit: 5);
    }

    /**
     * Quick action: Create new trip
     */
    public function createTrip()
    {
        return redirect()->route('trips.create');
    }

    /**
     * Quick action: Search destinations
     */
    public function searchDestinations()
    {
        return redirect()->route('destinations');
    }

    public function render()
    {
        return view('livewire.trip-dashboard', [
            'stats' => $this->stats,
            'upcomingTrips' => $this->upcomingTrips,
        ]);
    }
}
