<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::with(['customer', 'items.product'])
                    ->latest()
                    ->paginate($perPage);
    }

    public function create(array $payload)
    {
        return Order::create($payload);
    }

    public function findByUuid(string $uuid)
    {
        return Order::with(['customer', 'items.product'])
                    ->where('uuid', $uuid)
                    ->firstOrFail();
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

    /**
     * Output #2 — Create Order with Items in one transaction
     *
     * @param  string  $customerUuid
     * @param  array   $items  [['product_uuid' => '...', 'quantity' => 2], ...]
     */
    public function createWithItems(string $customerUuid, array $items): Order
    {
        return DB::transaction(function () use ($customerUuid, $items) {

            // 1. Resolve customer
            $customer = Customer::where('uuid', $customerUuid)->firstOrFail();

            // 2. Build line items & compute total
            $lineItems   = [];
            $totalAmount = 0;

            foreach ($items as $item) {
                $product   = Product::where('uuid', $item['product_uuid'])->firstOrFail();
                $unitPrice = $product->price;
                $subtotal  = $unitPrice * $item['quantity'];
                $totalAmount += $subtotal;

                $lineItems[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $unitPrice,
                ];
            }

            // 3. Create the order
            $order = Order::create([
                'customer_id'  => $customer->id,
                'total_amount' => $totalAmount,
            ]);

            // 4. Attach line items
            $order->items()->createMany($lineItems);

            // 5. Return with relationships
            return $order->load('items.product', 'customer');
        });
    }

    /**
     * Output #3 — Get all orders for a specific customer
     */
    public function getByCustomerUuid(string $customerUuid)
    {
        $customer = Customer::where('uuid', $customerUuid)->firstOrFail();

        return Order::with(['items.product'])
                    ->where('customer_id', $customer->id)
                    ->latest()
                    ->get();
    }
}