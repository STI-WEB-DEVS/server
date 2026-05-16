<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\Customer;

class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::with(['customer', 'items.product'])->latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        return Order::create($payload);
    }

    public function findByUuid(string $uuid)
    {
        return Order::with(['customer', 'items.product'])->where('uuid', $uuid)->firstOrFail();
    }

    public function findByField(string $field, $value)
    {
        return Order::where($field, $value)->firstOrFail();
    }

    public function findByCustomerUuid(string $customerUuid, int $perPage = 15)
    {
        $customer = Customer::where('uuid', $customerUuid)->firstOrFail();
        return Order::with(['customer', 'items.product'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate($perPage);
    }

    public function getSummary(string $from, string $to)
    {
        $revenue = Order::whereBetween('created_at', [$from, $to])->sum('total_amount');
        
        $customersCount = Order::whereBetween('created_at', [$from, $to])->distinct('customer_id')->count('customer_id');

        $topProducts = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', \Illuminate\Support\Facades\DB::raw('SUM(order_items.quantity) as total_quantity'), \Illuminate\Support\Facades\DB::raw('SUM(order_items.unit_price * order_items.quantity) as total_revenue'))
            ->whereBetween('orders.created_at', [$from, $to])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return [
            'revenue' => $revenue,
            'customers_count' => $customersCount,
            'top_products' => $topProducts
        ];
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
}