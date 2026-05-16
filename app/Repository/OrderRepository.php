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

    public function getRevenueByDateRange(string $from, string $to): float
    {
        return (float) Order::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->sum('total_amount');
    }

    public function getCustomerCountByDateRange(string $from, string $to): int
    {
        return (int) Order::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->distinct('customer_id')
            ->count('customer_id');
    }

    public function getTopProductsByDateRange(string $from, string $to, int $limit = 5): array
    {
        return \DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->whereBetween('orders.created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->select(
                'products.uuid as product_uuid',
                'products.name as product_name',
                \DB::raw('SUM(order_items.quantity) as total_quantity'),
                \DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.uuid', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function paginateByDateRange(string $from, string $to, int $perPage = 15)
    {
        return Order::with(['customer', 'items.product'])
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->latest()
            ->paginate($perPage);
    }

    public function getOrderCountByDateRange(string $from, string $to): int
    {
        return (int) Order::whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->count();
    }
}
