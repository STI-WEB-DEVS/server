<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRepository
{
    /**
     * Paginate orders, usually sorted by latest.
     */
    public function paginate(int $perPage = 15)
    {
        return Order::latest()->paginate($perPage);
    }

    /**
     * Create a new order.
     */
    public function create(array $payload)
    {
        return Order::create($payload);
    }

    /**
     * Find an order by its UUID.
     */
    public function findByUuid(string $uuid)
    {
        return Order::where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Find an order by a specific field (e.g., order_number).
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
     * Soft delete or hard delete an order.
     */
    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);
        return $model->delete();
    }

    /**
     * Restore a soft-deleted order.
     */
    public function restore(string $uuid)
    {
        $model = Order::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();
        return $model;
    }
}