<?php

namespace App\Repository;

use App\Models\Order;

class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::with(['customer', 'items.product'])
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $payload)
    {
        return Order::create($payload);
    }

    public function findByUuid(string $uuid)
    {
        return Order::with(['customer', 'items.product'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function update(string $uuid, array $payload)
    {
        $model = $this->findByUuid($uuid);
        $model->update($payload);
        return $model;
    }

    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);
        return $model->delete();
    }

    public function findByCustomerId(int $customerId)
    {
        return Order::with(['customer', 'items.product'])
            ->where('customer_id', $customerId)
            ->latest()
            ->get();
    }
}
