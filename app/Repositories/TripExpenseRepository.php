<?php

namespace App\Repositories;

use App\Models\TripExpense;

class TripExpenseRepository extends AbstractRepository
{
    public function __construct(TripExpense $model)
    {
        parent::__construct($model);
    }

    /**
     * Find expenses by trip ID
     */
    public function findByTripId($tripId)
    {
        return $this->model->where('trip_id', $tripId)->get();
    }

    /**
     * Find expenses by category
     */
    public function findByCategory($category)
    {
        return $this->model->where('category', $category)->get();
    }
}
