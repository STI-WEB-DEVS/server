<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::latest()->paginate($perPage);
    }

    public function createWithItems(int $customerId, float $totalAmount, array $items)
    {
        return DB::transaction(function () use ($customerId, $totalAmount, $items) {
            
            $order = Order::create([
                'uuid' => Str::uuid(),
                'customer_id' => $customerId,
                'total_amount' => $totalAmount,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            return Order::with('items.product')->find($order->id);
        });
    }

    public function getByCustomerId(int $customerId)
    {
        return Order::with('items.product')->where('customer_id', $customerId)->latest()->get();
    }

   public function findByUuid(string $uuid)
    {
        return Order::with('items')->where('uuid', $uuid)->firstOrFail();
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