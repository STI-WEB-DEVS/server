<?php

namespace App\Repository;

use App\Models\OrderItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderItemsRepository
{
       public function create(array $data): OrderItem
    {
        return OrderItem::create($data);
    }

    public function findByOrderId(int $orderId)
    {
        return OrderItem::where('order_id', $orderId)->get();
    }

    public function deleteByOrderId(int $orderId): bool
    {
        return OrderItem::where('order_id', $orderId)->delete();
    }




}
