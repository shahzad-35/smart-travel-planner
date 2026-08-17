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
     * Packing readiness for the next upcoming trip (fast, DB-only)
     */
    public function getNextTripReadinessProperty(): ?array
    {
        $next = $this->upcomingTrips->first();
        if (!$next) {
            return null;
        }

        $counts = \App\Models\PackingItem::where('trip_id', $next->id)
            ->selectRaw('COUNT(*) as total, SUM(is_packed) as packed')
            ->first();

        $total = (int) ($counts->total ?? 0);
        $packed = (int) ($counts->packed ?? 0);

        return [
            'trip' => $next,
            'total' => $total,
            'packed' => $packed,
            'percent' => $total > 0 ? (int) round($packed / $total * 100) : 0,
        ];
    }

    /**
     * Quick action: Create new trip
     */
    public function createTrip()
    {
        return $this->redirectRoute('trips.create');
    }

    /**
     * Quick action: Search destinations
     */
    public function searchDestinations()
    {
        return $this->redirectRoute('destinations');
    }

    public function render()
    {
        return view('livewire.trip-dashboard', [
            'stats' => $this->stats,
            'upcomingTrips' => $this->upcomingTrips,
        ]);
    }
}
