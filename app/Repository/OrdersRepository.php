<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrdersRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::latest()->paginate($perPage);
    }

    /**
     * Create a bulk order (multiple items) with transaction safety.
     */
    public function create(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            // 1. Identify the Customer
            $customer = Customer::where('uuid', $payload['customer_uuid'])->firstOrFail();
            
            // 2. Initialize the Order with a zero total
            $order = Order::create([
                'customer_id'  => $customer->id,
                'total_amount' => 0, 
            ]);

            $runningTotal = 0;

            // 3. Loop through the "items" array from your Hoppscotch request
            foreach ($payload['items'] as $item) {
                // Find product and lock it to prevent simultaneous data errors
                $product = Product::where('uuid', $item['product_uuid'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $lineTotal = $product->price * $item['quantity'];
                $runningTotal += $lineTotal;

                // Create the individual line item
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                ]);
            }

            // 4. Update the final total after all items are added
            $order->update(['total_amount' => $runningTotal]);

            return $order->load('items.product');
        });
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