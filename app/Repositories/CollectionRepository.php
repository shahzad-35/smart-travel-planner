<?php

namespace App\Repositories;

use App\Models\Collection;

class CollectionRepository extends AbstractRepository
{
    public function __construct(Collection $model)
    {
        parent::__construct($model);
    }

    /**
     * Find collections by user ID
     */
    public function findByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }
}
