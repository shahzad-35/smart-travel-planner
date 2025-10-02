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
}
