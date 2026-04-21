<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Resources\OrderResource;
class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
            return Order::create([
            'uuid'         => $payload['uuid'],
            'customer_id'  => $payload['customer_id'], // This must be the ID, not UUID
            'total_amount' => $payload['total_amount'] ?? 0,
        ]);
    }

    public function createItem(array $data)
    {
            return OrderItem::create([
            'uuid'       => $data['uuid'], // Add this if your order_items has a uuid column
            'order_id'   => $data['order_id'],   
            'product_id' => $data['product_id'], 
            'quantity'   => $data['quantity'],
            'unit_price' => $data['unit_price'] ?? 0,
        ]);
    }

    public function findByUuid(string $uuid)
    {
        return Order::where('uuid', $uuid)->firstOrFail();
    }

    public function findByField(string $field, $value)
    {
        // Changed to get() to return all orders for a customer
        return Order::where($field, $value)->get();
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
    public function findByCustomerUuid(string $uuid)
{
    return \App\Models\Order::whereHas('customer', function($query) use ($uuid) {
        $query->where('uuid', $uuid);
    })->with(['items.product', 'customer'])->get();
}
}