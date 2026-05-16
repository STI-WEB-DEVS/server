<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    // Inclusive date range helper — covers the full 'to' day up to 23:59:59
    private function applyDateRange($query, ?string $from, ?string $to)
    {
        if ($from && $to) {
            $query->whereBetween('orders.created_at', [
                $from . ' 00:00:00',
                $to   . ' 23:59:59',
            ]);
        }
        return $query;
    }

    public function getTotalRevenue(?string $from, ?string $to): float
    {
        $query = Order::query();
        $this->applyDateRange($query, $from, $to);
        return (float) $query->sum('total_amount');
    }

    public function getUniqueCustomerCount(?string $from, ?string $to): int
    {
        $query = Order::query();
        $this->applyDateRange($query, $from, $to);
        return $query->distinct('customer_id')->count('customer_id');
    }

    public function getTopProducts(?string $from, ?string $to, int $limit = 5): array
    {
        $query = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id');

        if ($from && $to) {
            $query->whereBetween('orders.created_at', [
                $from . ' 00:00:00',
                $to   . ' 23:59:59',
            ]);
        }

        return $query->select(
                'products.uuid',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.uuid', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}