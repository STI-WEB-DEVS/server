<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * GET /api/summary
     * Returns total_revenue, total_customers, top_products
     * Accepts optional ?from=YYYY-MM-DD&to=YYYY-MM-DD query params
     */
    public function summary(Request $request)
    {
        $query = Order::query();

        // Apply date range filter if provided
        if ($request->has('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->has('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        // Total revenue
        $totalRevenue = (clone $query)->sum('total_amount');

        // Total unique customers who ordered
        $totalCustomers = (clone $query)->distinct('customer_id')->count('customer_id');

        // Top 5 most purchased products (by total quantity sold)
        $orderIds = (clone $query)->pluck('id');

        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereIn('order_id', $orderIds)
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->with('product:id,name,uuid,price')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Unknown',
                    'total_sold' => (int) $item->total_sold,
                ];
            });

        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_customers' => $totalCustomers,
            'top_products' => $topProducts,
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * GET /api/weekly-chart
     * Returns products sold per day for the current week (Mon-Sun)
     */
    public function weeklyChart(Request $request)
    {
        // Determine the week range
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $now->copy()->endOfWeek(Carbon::SUNDAY);

        // Get all order items with their order dates for this week
        $dailySales = OrderItem::select(
                DB::raw('DATE(orders.created_at) as sale_date'),
                'order_items.product_id',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$startOfWeek, $endOfWeek])
            ->groupBy('sale_date', 'order_items.product_id')
            ->with('product:id,name')
            ->get();

        // Build labels for each day of the week
        $labels = [];
        for ($i = 0; $i < 7; $i++) {
            $labels[] = $startOfWeek->copy()->addDays($i)->format('D (M d)');
        }

        // Build date keys
        $dateKeys = [];
        for ($i = 0; $i < 7; $i++) {
            $dateKeys[] = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
        }

        // Collect unique products
        $products = $dailySales->pluck('product.name', 'product_id')->unique();

        // Build datasets per product
        $datasets = [];
        $colors = [
            'rgba(99, 102, 241, 0.8)',   // indigo
            'rgba(168, 85, 247, 0.8)',    // purple
            'rgba(236, 72, 153, 0.8)',    // pink
            'rgba(14, 165, 233, 0.8)',    // sky
            'rgba(34, 197, 94, 0.8)',     // green
            'rgba(245, 158, 11, 0.8)',    // amber
            'rgba(239, 68, 68, 0.8)',     // red
            'rgba(20, 184, 166, 0.8)',    // teal
        ];

        $colorIndex = 0;
        foreach ($products as $productId => $productName) {
            $data = [];
            foreach ($dateKeys as $dateKey) {
                $sold = $dailySales
                    ->where('product_id', $productId)
                    ->where('sale_date', $dateKey)
                    ->first();
                $data[] = $sold ? (int) $sold->total_sold : 0;
            }

            $color = $colors[$colorIndex % count($colors)];
            $datasets[] = [
                'label' => $productName,
                'data' => $data,
                'backgroundColor' => $color,
                'borderColor' => str_replace('0.8', '1', $color),
                'borderWidth' => 2,
                'borderRadius' => 6,
            ];
            $colorIndex++;
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => $datasets,
            'week_start' => $startOfWeek->format('Y-m-d'),
            'week_end' => $endOfWeek->format('Y-m-d'),
        ]);
    }
}
