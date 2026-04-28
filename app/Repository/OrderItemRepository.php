<?php

namespace App\Repository;

use App\Models\OrderItem;

class OrderItemRepository
{
   public function create(array $data): OrderItem
    {
        return OrderItem::create([
            'order_id'   => $data['order_id'],
            'product_id' => $data['product_id'],
            'quantity'   => $data['quantity'],
            'unit_price' => $data['unit_price'],
        ]);
    }

    public function createMany(array $items)
    {
        return OrderItem::insert($items);
    }
}
