<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB as DB;

class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::latest()->paginate($perPage);
    }

    public function create(array $order, array $orderItem)
    {
        return DB::transaction(function () use ($order, $orderItem){
            $orderCreated = Order::create($order);
            foreach($orderItem as $orderItem)
                {
                    $orderItem['order_id'] = $orderCreated['id'];
                    OrderItem::create($orderItem);
                }
            return $orderCreated;
        });
    }

    public function findByCustomerUuid(int $uuid)
    {
        $customerOrders = Order::where('customer_id', $uuid)->get();

        $result = [];

        foreach ($customerOrders as $order) {
            $orderItems = OrderItem::where('order_id', $order['id'])->get();

            $result[] = [
                'order'       => $order,
                'order_items' => $orderItems,
            ];
        }

        return $result;
    }

    public function findByUuid(string $uuid)
    {
        return Order::where('uuid', $uuid)->first();
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