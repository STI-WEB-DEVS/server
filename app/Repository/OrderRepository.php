<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;


class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::latest()->paginate($perPage);
    }

    public function create(array $orderData, array $orderItemsData)
    {
        return DB::transaction(function () use ($orderData, $orderItemsData) {
            
            $order = Order::create($orderData);  
            foreach ($orderItemsData as $item) {
              
                $item['order_id'] = $order->id; 
                OrderItem::create($item);
            }
            
            return $order; 
        });
    }
    public function findByCustomerUuid(string $customerUuid)
    {
        return Order::with('items')
            ->where('customer_id', $customerUuid) 
            ->latest()
            ->get();
    }

    public function findByUuid(string $uuid)
    {
        return Order::where('uuid', $uuid)->firstOrFail();
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