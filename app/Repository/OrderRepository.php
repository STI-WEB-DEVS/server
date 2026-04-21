<?php

namespace App\Repository;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    public function create(array $payload): Order
    {
        return DB::transaction(function () use ($payload) {
            $order = Order::create([
                'customer_id' => $payload['customer_id'],
                'total_amount' => $payload['total_amount'],
            ]);

            $order->items()->createMany($payload['items']);

            return $order->load(['customer', 'items.product']);
        });
    }

    public function listByCustomerUuid(string $customerUuid, ?int $limit = null)
    {
        $customer = Customer::where('uuid', $customerUuid)->firstOrFail();

        $query = Order::with(['customer', 'items.product'])
            ->whereHas('items')
            ->where('customer_id', $customer->id)
            ->latest();

        if (! is_null($limit) && $limit > 0) {
            $query->limit($limit);
        }

        return $query->get();
    }
}