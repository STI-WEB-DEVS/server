<?php

namespace App\Repository;

use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRepository
{
    public function findByUuid(string $uuid): ?Order
    {
        return Order::with(['items.product', 'customer'])
                    ->where('uuid', $uuid)
                    ->first();
    }

    public function paginate(int $perPage = 15)
    {
        return Order::with(['items.product', 'customer'])->paginate($perPage);
    }

    public function findById(int $id): ?Order
    {
        return Order::find($id);
    }

    public function findByField(string $field, $value): ?Order
    {
        return Order::where($field, $value)->first();
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(string $uuid, array $data): Order
    {
        $order = $this->findByUuid($uuid);
        $order->update($data);
        return $order;
    }

    public function delete(string $uuid): bool
    {
        $order = $this->findByUuid($uuid);
        return $order->delete();
    }

    public function restore(string $uuid): ?Order
    {
        $order = Order::withTrashed()->where('uuid', $uuid)->first();
        $order->restore();
        return $order;
    }
}