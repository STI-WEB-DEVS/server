<?php

namespace App\Repository;

use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRepository
{
    /**
     * Get paginated orders.
     */
    public function paginate(int $perPage = 15)
    {
        return Order::latest()->paginate($perPage);
    }

    /**
     * Create a new order record.
     */
    public function create(array $payload)
    {
        return Order::create($payload);
    }

    /**
     * Find a specific order by its UUID.
     */
    public function findByUuid(string $uuid)
    {
        return Order::where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Find an order by a specific column and value.
     */
    public function findByField(string $field, $value)
    {
        return Order::where($field, $value)->firstOrFail();
    }

    /**
     * Update an existing order by UUID.
     */
    public function update(string $uuid, array $payload)
    {
        $model = $this->findByUuid($uuid);
        $model->update($payload);
        return $model;
    }

    /**
     * Delete an order by UUID.
     */
    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);
        return $model->delete();
    }

    /**
     * Restore a soft-deleted order by UUID.
     */
    public function restore(string $uuid)
    {
        $model = Order::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();
        return $model;
    }
}