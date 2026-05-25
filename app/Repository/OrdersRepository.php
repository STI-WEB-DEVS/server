<?php

namespace App\Repository;

use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class OrdersRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::latest()->paginate($perPage);
    }

    public function paginateByCustomer(int $customerId, int $perPage = 15)
    {
        return Order::with('items')->where('customer_id', $customerId)->latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        return Order::create($payload);
    }

    public function createWithItems(array $orderData, array $itemData)
    {
        return DB::transaction(function () use ($orderData, $itemData){
            
            $order = Order::create($orderData);

            $order->items()->createMany($itemData);

            return $order->load('items');
        });
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
}