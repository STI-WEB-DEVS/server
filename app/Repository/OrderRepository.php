<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::with(['customer','items.product'])
        ->latest()
        ->paginate($perPage);
    }

    public function create(array $payload)
    {
        return Order::create($payload);
    }

    public function findByUuid(string $uuid)
    {   
        return Order::with(['customer','items.product'])
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

    public function getOrdersByCustomer($customerId)
    {
        return Order::where('customer_id', $customerId)->get();
    }

     public function getSummary(?string $from, ?string $to): array
    {
        // ← If no dates provided, query all orders (no date filter)
        $ordersQuery = Order::query();
        if ($from && $to) {
            $ordersQuery->whereBetween('created_at', [
                $from . ' 00:00:00',
                $to   . ' 23:59:59',
            ]);
        }
 
        $totalRevenue  = (clone $ordersQuery)->sum('total_amount');
        $customerCount = (clone $ordersQuery)->distinct('customer_id')->count('customer_id');
 
        // ← Same logic for top products
        $topProductsQuery = OrderItem::query();
        if ($from && $to) {
            $topProductsQuery->whereHas('order', function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [
                    $from . ' 00:00:00',
                    $to   . ' 23:59:59',
                ]);
            });
        }
 
        $topProducts = $topProductsQuery
            ->selectRaw('product_id, SUM(quantity) as total_quantity')
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->map(fn($item) => [
                'product_name'    => $item->product->name ?? 'Unknown',
                'total_purchased' => (int) $item->total_quantity,
            ])
            ->values();
 
        return [
            'total_revenue'  => (float) $totalRevenue,
            'customer_count' => (int)   $customerCount,
            'top_products'   => $topProducts,
            'date_range'     => ['from' => $from, 'to' => $to],
        ];
    }
}