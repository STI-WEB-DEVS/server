<?php

namespace App\Repository;

use App\Models\Order;

class OrderRepository
{
    public function create(array $payload): Order
    {
        return Order::create($payload);
    }

    public function createItem(Order $order, array $payload)
    {
        return $order->items()->create($payload);
    }

    public function paginateByCustomerId(int $customerId, int $perPage = 15)
    {
        return Order::where('customer_id', $customerId)
            ->with(['items.product'])
            ->latest()
            ->paginate($perPage);
    }

    public function listByCustomerId(int $customerId)
    {
        return Order::where('customer_id', $customerId)
            ->with(['customer', 'items.product'])
            ->latest()
            ->get();
    }
}
