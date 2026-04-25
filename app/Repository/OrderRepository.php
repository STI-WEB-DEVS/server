<?php

namespace App\Repository;

use App\Models\Order;

class OrderRepository
{
    public function findByUuid(string $uuid): Order
    {
        return Order::with(['items.product', 'customer'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }

    public function paginate(int $perPage = 15)
    {
        return Order::with(['items.product', 'customer'])
            ->latest()
            ->paginate($perPage);
    }

    public function findByCustomerUuid(string $customerUuid, int $perPage = 15)
    {
        return Order::with(['items.product', 'customer'])
            ->whereHas('customer', function ($query) use ($customerUuid) {
                $query->where('uuid', $customerUuid);
            })
            ->latest()
            ->paginate($perPage);
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
}