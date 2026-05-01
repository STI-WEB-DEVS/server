<?php

namespace App\Repository; // Note: Ensure this matches your folder structure!

use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository
{
    protected Order $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $payload): Order
    {
        return $this->model->newQuery()->create($payload);
    }

    public function findByUuid(string $uuid): ?Order
    {
        return $this->model->newQuery()
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function findByField(string $field, $value): ?Order
    {
        return $this->model->newQuery()
            ->where($field, $value)
            ->firstOrFail();
    }

    public function update(string $uuid, array $payload): Order
    {
        $order = $this->findByUuid($uuid);
        $order->update($payload);
        return $order;
    }

    public function delete(string $uuid): bool
    {
        $order = $this->findByUuid($uuid);
        return $order->delete();
    }

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