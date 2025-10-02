<?php

namespace App\Repositories;

interface BaseRepository
{
    /**
     * Get all records
     */
    public function all();

    /**
     * Find a record by ID
     */
    public function find($id);

    /**
     * Create a new record
     */
    public function create(array $data);

    /**
     * Update a record by ID
     */
    public function update(array $data, int $id);

    /**
     * Delete a record by ID
     */
    public function delete($id);

    /**
     * Find records by criteria
     */
    public function findBy(array $criteria);

    /**
     * Get paginated records
     */
    public function paginate($perPage = 15);
}
