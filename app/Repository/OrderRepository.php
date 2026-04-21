<?php

namespace App\Repository;

use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        return Order::create([
            'uuid' => $payload['order_uuid'], // Uses the UUID from your input
            'customer_uuid' => $payload['customer_uuid'] ?? 'default-uuid', 
            'total_amount' => $payload['total_amount'] ?? 0, 
            'status' => 'pending',
        ]);
    }
    public function createOrderItem($orderUuid, array $itemData)
    {
        return Order::create([
            'uuid' => Str::uuid(),
            'order_uuid' => $orderUuid,
            'product_uuid' => $itemData['product_uuid'],
            'quantity' => $itemData['quantity'],
            'price' => $itemData['price'] // snapshot of price at time of order
        ]);
    }

    public function findByUuid(string $uuid)
    {
        return Order::where('customer_uuid', $uuid)
                ->with('product')
                ->orderBy('created_at', 'desc')
                ->get();
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