<?php

namespace App\Repositories;

use App\Models\Favorite;

class FavoriteRepository extends AbstractRepository
{
    public function __construct(Favorite $model)
    {
        parent::__construct($model);
    }

    /**
     * Find favorites by user ID
     */
    public function findByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }
}
