<?php

namespace App\Service;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderSummaryService
{
    /**
     * Get order summary with optional date range.
     *
     * @param string|null $from
     * @param string|null $to
     * @return array
     */
    public function getSummary(?string $from = null, ?string $to = null): array
    {
        // build base query for orders with optional date filters
        $ordersBase = function () use ($from, $to) {
            $q = Order::query();
            if ($from) {
                $q->whereDate('created_at', '>=', $from);
            }
            if ($to) {
                $q->whereDate('created_at', '<=', $to);
            }
            return $q;
        };

        $totalRevenue = $ordersBase()->sum('total_amount') ?: 0;
        // use distinct() then count the customer_id column to reliably count unique customers
        $totalCustomers = $ordersBase()->distinct()->count('customer_id') ?: 0;

        $topProductsQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->when($from, function ($q) use ($from) {
                $q->whereDate('orders.created_at', '>=', $from);
            })
            ->when($to, function ($q) use ($to) {
                $q->whereDate('orders.created_at', '<=', $to);
            })
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $topProducts = $topProductsQuery->map(function ($row) {
            return [
                'name' => $row->name,
                'total_sold' => (int) $row->total_sold,
            ];
        })->toArray();

        return [
            'total_revenue' => (float) $totalRevenue,
            'total_customers' => (int) $totalCustomers,
            'top_products' => $topProducts,
        ];
    }
}
