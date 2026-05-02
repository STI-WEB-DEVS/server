<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::with('items')->latest()->paginate($perPage);
    }

    public function paginateForCustomer(int $customerId, int $perPage = 15)
    {
        return Order::with('items')
            ->where('customer_id', $customerId)
            ->latest()
            ->paginate($perPage);
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

    public function restore(string $uuid)
    {
        $model = Order::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();
        return $model;
    }

    // Custom
    public function getAll()
    {
        return Order::with('items')->get();
    }

    public function createItem(array $payload)
    {
        return OrderItem::create($payload);
    }
}
