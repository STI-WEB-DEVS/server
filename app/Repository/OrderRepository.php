<?php

namespace App\Repository;

use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::latest()->paginate($perPage);
    }

    public function paginateWithRelations(int $perPage = 15)
    {
        return Order::with(['customer', 'items.product'])->latest()->paginate($perPage);
    }

    public function paginateByField(string $field, $value, int $perPage = 15)
    {
        return Order::where($field, $value)->latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        return Order::create($payload);
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

    public function getByCustomerId(int $customerId)
    {
        return Order::with(['customer', 'items.product'])
            ->where('customer_id', $customerId)
            ->get();
    }

    public function getTotalRevenue($from, $to)
    {
        return Order::whereBetween('created_at', [$from, $to])->sum('total_amount');
    }

    public function getCustomerCount($from, $to)
    {
        return Order::whereBetween('created_at', [$from, $to])->distinct('customer_id')->count();
    }

    public function getOrderCount($from, $to)
    {
        return Order::whereBetween('created_at', [$from, $to])->count();
    }

    public function getTopProducts($from, $to, $limit = 5)

    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_purchased'))
            ->whereBetween('order_items.created_at', [$from, $to])
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_purchased')
            ->limit($limit)
            ->get();
    }

    public function getRecentOrders($from, $to, $limit = 5)
    {
        return Order::with('customer')
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
