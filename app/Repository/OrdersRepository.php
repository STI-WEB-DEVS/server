<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrdersRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        // 1. Resolve customer UUID to numeric id
        // This fixes the "Undefined array key" error by using 'customer_uuid'
        $customer = Customer::where('uuid', $payload['customer_uuid'])->firstOrFail();

        // 2. Resolve product UUID to numeric id
        $product = Product::where('uuid', $payload['product_uuid'])->firstOrFail();

        // 3. Calculate total based on the single product and quantity
        $total = $product->price * $payload['quantity'];

        // 4. Create the main Order
        $order = Order::create([
            'customer_id'  => $customer->id,
            'total_amount' => $total,
        ]);

        // 5. Create the Order Item entry
        OrderItem::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => $payload['quantity'],
            'unit_price' => $product->price,
        ]);

        // Return the order with its items and product details for the API response
        return $order->load('items.product');
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