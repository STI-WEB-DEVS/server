<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class OrderSummaryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_FORBIDDEN);
        }

        $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        $ordersQuery = Order::query()
            ->when($request->from_date, function ($query, $fromDate) {
                $query->whereDate('created_at', '>=', $fromDate);
            })
            ->when($request->to_date, function ($query, $toDate) {
                $query->whereDate('created_at', '<=', $toDate);
            });

        $totalOrders = $ordersQuery->count();
        $totalRevenue = (float) $ordersQuery->sum('total_amount');
        $averageOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;
        $uniqueCustomers = $ordersQuery->distinct('customer_id')->count('customer_id');

        $topProducts = DB::table('order_items')
            ->select([
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue'),
            ])
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->when($request->from_date, function ($query, $fromDate) {
                $query->whereDate('orders.created_at', '>=', $fromDate);
            })
            ->when($request->to_date, function ($query, $toDate) {
                $query->whereDate('orders.created_at', '<=', $toDate);
            })
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $ordersByDayQuery = Order::query()
            ->when($request->from_date, function ($query, $fromDate) {
                $query->whereDate('created_at', '>=', $fromDate);
            })
            ->when($request->to_date, function ($query, $toDate) {
                $query->whereDate('created_at', '<=', $toDate);
            });

        $ordersByDay = $ordersByDayQuery
            ->selectRaw('DATE(created_at) as day, COUNT(*) as order_count, SUM(total_amount) as revenue')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_orders' => $totalOrders,
                'total_revenue' => number_format($totalRevenue, 2, '.', ''),
                'average_order_value' => number_format($averageOrderValue, 2, '.', ''),
                'unique_customers' => $uniqueCustomers,
                'top_products' => $topProducts,
                'orders_by_day' => $ordersByDay,
            ],
        ]);
    }
}
