<?php

namespace App\Livewire;

use App\Models\Trip;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TravelStats extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = Auth::id();
        $cacheKey = "user_{$userId}_travel_stats";

        $this->stats = Cache::remember($cacheKey, 3600, function () use ($userId) {
            return $this->calculateStats($userId);
        });
    }

    private function calculateStats($userId)
    {
        $trips = Trip::where('user_id', $userId)->get();

        $totalTrips = $trips->count();
        $completedTrips = $trips->where('status', 'completed')->count();
        $plannedTrips = $trips->where('status', 'planned')->count();
        $cancelledTrips = $trips->where('status', 'cancelled')->count();
        $ongoingTrips = $trips->where('status', 'ongoing')->count();

        $uniqueCountries = $trips->pluck('country_code')->unique()->count();
        
        $totalTravelDays = $trips->where('status', 'completed')->sum(function ($trip) {
            return $trip->start_date && $trip->end_date 
                ? $trip->start_date->diffInDays($trip->end_date) + 1 
                : 0;
        });

        $totalBudgetSpent = $trips->where('status', 'completed')->sum('budget');

        // Most visited destinations
        $topDestinations = Trip::where('user_id', $userId)
            ->where('status', 'completed')
            ->select('destination', DB::raw('count(*) as count'))
            ->groupBy('destination')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Most common trip type
        $mostCommonType = Trip::where('user_id', $userId)
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->orderByDesc('count')
            ->first();

        // Travel Timeline (By Year and Month)
        // We will structure this for Chart.js: { labels: ['Jan 2024', ...], data: [1, 0, ...] }
        // Simple implementation: trips per year for last 5 years
        $tripsPerYear = Trip::where('user_id', $userId)
            ->whereIn('status', ['completed', 'planned', 'ongoing'])
            ->selectRaw('YEAR(start_date) as year, COUNT(*) as count')
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('count', 'year')
            ->toArray();
            
        // For Pie Chart: Status Distribution
        $statusDistribution = [
            'Planned' => $plannedTrips,
            'Ongoing' => $ongoingTrips,
            'Completed' => $completedTrips,
            'Cancelled' => $cancelledTrips,
        ];

        return [
            'total_trips' => $totalTrips,
            'completed_trips' => $completedTrips,
            'planned_trips' => $plannedTrips,
            'cancelled_trips' => $cancelledTrips,
            'ongoing_trips' => $ongoingTrips,
            'unique_countries' => $uniqueCountries,
            'total_travel_days' => $totalTravelDays,
            'total_budget_spent' => $totalBudgetSpent,
            'top_destinations' => $topDestinations->map(fn($item) => ['destination' => $item->destination, 'count' => $item->count])->toArray(),
            'most_common_trip_type' => $mostCommonType ? $mostCommonType->type : 'N/A',
            'trips_per_year' => $tripsPerYear,
            'status_distribution' => $statusDistribution,
        ];
    }

    public function downloadPdf()
    {
        $data = [
            'stats' => $this->stats,
            'user' => Auth::user(),
            'generated_at' => Carbon::now(),
        ];

        $pdf = Pdf::loadView('pdf.travel-stats', $data);

        return $this->streamDownload(function () use ($pdf) {
            return $pdf->output();
        }, 'travel-stats.pdf');
    }

    public function refreshStats()
    {
        Cache::forget("user_" . Auth::id() . "_travel_stats");
        $this->loadStats();
        $this->dispatch('stats-refreshed', stats: $this->stats);
    }

    public function render()
    {
        return view('livewire.travel-stats');
    }
}
