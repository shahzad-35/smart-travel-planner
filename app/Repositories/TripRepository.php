<?php

namespace App\Repositories;

use App\Models\Trip;

class TripRepository extends AbstractRepository
{
    public function __construct(Trip $model)
    {
        parent::__construct($model);
    }

    /**
     * Find trips by user ID
     */
    public function findByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    /**
     * Find upcoming trips for a user
     */
    public function findUpcomingTrips($userId)
    {
        return $this->model->where('user_id', $userId)
                          ->where('start_date', '>', now())
                          ->orderBy('start_date')
                          ->get();
    }

    /**
     * Find active trips (not completed)
     */
    public function findActiveTrips($userId)
    {
        return $this->model->where('user_id', $userId)
                          ->where('status', '!=', 'completed')
                          ->get();
    }

    /**
     * Find trips that conflict with the given date range
     */
    public function findConflictingTrips($userId, $startDate, $endDate)
    {
        return $this->model->where('user_id', $userId)
                          ->where('status', '!=', 'completed')
                          ->where('status', '!=', 'cancelled')
                          ->where(function ($query) use ($startDate, $endDate) {
                              // Check for overlapping date ranges
                              $query->where(function ($q) use ($startDate, $endDate) {
                                  // New trip starts during existing trip
                                  $q->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $startDate);
                              })->orWhere(function ($q) use ($startDate, $endDate) {
                                  // New trip ends during existing trip
                                  $q->where('start_date', '<=', $endDate)
                                    ->where('end_date', '>=', $endDate);
                              })->orWhere(function ($q) use ($startDate, $endDate) {
                                  // New trip completely encompasses existing trip
                                  $q->where('start_date', '>=', $startDate)
                                    ->where('end_date', '<=', $endDate);
                              })->orWhere(function ($q) use ($startDate, $endDate) {
                                  // Existing trip completely encompasses new trip
                                  $q->where('start_date', '<=', $startDate)
                                    ->where('end_date', '>=', $endDate);
                              });
                          })
                          ->get();
    }

    public function getFilteredTrips($userId, array $filters = [], string $sortBy = 'start_date', string $sortDirection = 'desc', string $search = '', int $perPage = 10)
    {
        $query = $this->model->where('user_id', $userId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['destination'])) {
            $query->where('destination', 'like', '%' . $filters['destination'] . '%');
        }

        if (!empty($filters['date_from'])) {
            $query->where('start_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('end_date', '<=', $filters['date_to']);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('destination', 'like', '%' . $search . '%')
                  ->orWhere('notes', 'like', '%' . $search . '%')
                  ->orWhere('type', 'like', '%' . $search . '%');
            });
        }

        $allowedSortFields = ['start_date', 'end_date', 'destination', 'budget', 'type', 'status', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('start_date', 'desc');
        }

        return $query->paginate($perPage);
    }

    public function getTripStats($userId)
    {
        $totalTrips = $this->model->where('user_id', $userId)->count();

        $countriesVisited = $this->model->where('user_id', $userId)
                                       ->where('status', 'completed')
                                       ->distinct('country_code')
                                       ->count('country_code');

        $upcomingCount = $this->model->where('user_id', $userId)
                                    ->where('start_date', '>', now())
                                    ->where('status', '!=', 'cancelled')
                                    ->count();

        return [
            'total_trips' => $totalTrips,
            'countries_visited' => $countriesVisited,
            'upcoming_count' => $upcomingCount,
        ];
    }

    public function getUpcomingTripsForTimeline($userId, $limit = 5)
    {
        return $this->model->where('user_id', $userId)
                          ->where('start_date', '>', now())
                          ->where('status', '!=', 'cancelled')
                          ->orderBy('start_date')
                          ->limit($limit)
                          ->get();
    }
}
