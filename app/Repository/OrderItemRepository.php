<?php

namespace App\Repository;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderItemRepository
{
    public function paginate(int $perPage = 15)
    {
        return OrderItem::latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        return OrderItem::create($payload);
    }

    public function findByUuid(string $uuid)
    {
        return OrderItem::where('uuid', $uuid)->firstOrFail();
    }

    public function findByField(string $field, $value)
    {
        return OrderItem::where($field, $value)->firstOrFail();
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
        $model = OrderItem::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();
        return $model;
    }

    public function getByOrderId(int $orderId)
    {
        return OrderItem::with(['product', 'order.items'])->where('order_id', $orderId)->get(['id', 'order_id', 'product_id', 'quantity', 'unit_price']);
    }

    public function getByOrderIds(array $orderIds)
    {
        return OrderItem::with(['product', 'order.items'])->whereIn('order_id', $orderIds)->get(['id', 'order_id', 'product_id', 'quantity', 'unit_price']);
    }
}