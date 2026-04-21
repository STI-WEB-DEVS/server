<?php

namespace App\Repository;

use App\Models\OrderItem;

class OrderItemRepository
{
    public function create(array $payload)
    {
        return OrderItem::create($payload);
    }

    public function createMany(array $items)
    {
        return OrderItem::insert($items);
    }
}
