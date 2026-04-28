<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\OrderItem; // Ensure this is imported

class OrderRepository
{
    /**
     * FIX: Added the missing all() method called by OrderService
     */
    public function all()
    {
        return Order::with(['customer', 'items.product'])
            ->latest()
            ->get();
    }

    public function create(array $payload): Order
    {
        return Order::create($payload);
    }

    /**
     * FIX: Updated to handle the payload directly 
     * matching how the Service calls it: createItem($itemData)
     */
    public function createItem(array $payload)
    {
        return OrderItem::create($payload);
    }

    /**
     * Requirement: Return a list of orders made by a customer uuid.
     * Use this if you are filtering by the string UUID.
     */
    public function findByCustomerUuid(string $customerUuid)
    {
        return Order::whereHas('customer', function ($query) use ($customerUuid) {
            $query->where('uuid', $customerUuid);
        })
        ->with(['customer', 'items.product'])
        ->latest()
        ->get();
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