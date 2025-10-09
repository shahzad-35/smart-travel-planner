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
}
