<?php

namespace App\Repository;

use App\Models\Order;

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

    public function findByCustomerId(int $customerId)
    {
        return Order::with(['customer', 'items.product'])
            ->where('customer_id', $customerId)
            ->latest()
            ->get();
    }

    public function getSummaryData(string $from, string $to)
    {
        $to .= ' 23:59:59';
        
        $revenue = Order::whereBetween('created_at', [$from, $to])->sum('total_amount');
        
        $customerCount = Order::whereBetween('created_at', [$from, $to])
            ->distinct('customer_id')
            ->count('customer_id');

        $topProducts = \App\Models\OrderItem::whereHas('order', function($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            })
            ->select('product_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->with('product')
            ->get()
            ->map(function($item) {
                return [
                    'name' => $item->product->name,
                    'total_quantity' => (int) $item->total_quantity,
                ];
            });

        return [
            'total_revenue' => (float) $revenue,
            'customer_count' => $customerCount,
            'top_products' => $topProducts,
        ];
    }
}
