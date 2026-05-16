<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
 
    public function analytics(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $orderQuery = Order::query();
        if ($startDate) {
            $orderQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $orderQuery->whereDate('created_at', '<=', $endDate);
        }

        $totalRevenue = $orderQuery->sum('total_amount');

        $totalCustomers = $orderQuery->distinct('customer_id')->count('customer_id');

        $topProducts = OrderItem::query()
            ->selectRaw('products.uuid, products.name, SUM(order_items.quantity) as total_quantity, COUNT(DISTINCT order_items.order_id) as total_orders')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->when($startDate, function ($q) use ($startDate) {
                return $q->whereDate('orders.created_at', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                return $q->whereDate('orders.created_at', '<=', $endDate);
            })
            ->groupBy('products.id', 'products.uuid', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_customers' => $totalCustomers,
            'top_products' => $topProducts,
        ]);
    }
}
