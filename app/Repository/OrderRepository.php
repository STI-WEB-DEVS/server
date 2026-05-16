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

    public function getSummary(array $filters = [])
    {
        $query = Order::query();
        $itemQuery = \App\Models\OrderItem::query();

        if (!empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
            $itemQuery->whereHas('order', function($q) use ($filters) {
                $q->whereDate('created_at', '>=', $filters['from']);
            });
        }

        if (!empty($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
            $itemQuery->whereHas('order', function($q) use ($filters) {
                $q->whereDate('created_at', '<=', $filters['to']);
            });
        }

        $totalAmount = $query->sum('total_amount');
        $customersCount = $query->distinct('customer_id')->count('customer_id');
        
        $topProducts = $itemQuery->select('product_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_quantity'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return [
            'total_amount' => $totalAmount,
            'customers_count' => $customersCount,
            'top_products' => $topProducts
        ];
    }
}
