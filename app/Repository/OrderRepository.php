<?php

namespace App\Repository;

use App\Models\Order;

class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::latest()->paginate($perPage);
    }

    public function paginateWithDetails(int $perPage = 15)
    {
        return Order::with(['customer', 'items.product'])->latest()->paginate($perPage);
    }

    public function getByCustomerId(int $customerId)
    {
        return Order::with(['items.product', 'customer'])
            ->where('customer_id', $customerId)
            ->latest()
            ->get();
    }

    public function create(array $payload)
    {
        return Order::create($payload);
    }

    public function findByUuid(string $uuid)
    {
        return Order::where('uuid', $uuid)->firstOrFail();
    }

    public function findByField(string $field, $value)
    {
        return Order::where($field, $value)->firstOrFail();
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
}
