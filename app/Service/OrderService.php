<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository
{
    protected Order $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    /**
     * Get paginated orders
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new order
     */
    public function create(array $payload): Order
    {
        return $this->model->newQuery()->create($payload);
    }

    /**
     * Find an order by UUID
     */
    public function findByUuid(string $uuid): ?Order
    {
        return $this->model->newQuery()
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    /**
     * Find by a custom field
     */
    public function findByField(string $field, $value): ?Order
    {
        return $this->model->newQuery()
            ->where($field, $value)
            ->firstOrFail();
    }

    /**
     * Update an order by UUID
     */
    public function update(string $uuid, array $payload): Order
    {
        $order = $this->findByUuid($uuid);
        $order->update($payload);
        return $order;
    }

    /**
     * Delete an order (supports Soft Deletes if enabled in Model)
     */
    public function delete(string $uuid): bool
    {
        $order = $this->findByUuid($uuid);
        return $order->delete();
    }

    /**
     * Restore a soft-deleted order
     */
    public function restore(string $uuid): Order
    {
        $order = $this->model->newQuery()
            ->withTrashed()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $order->restore();
        return $order;
    }
}