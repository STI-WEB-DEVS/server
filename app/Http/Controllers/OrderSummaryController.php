<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrderSummaryController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $from = $request->date('from')->startOfDay();
        $to = $request->date('to')->endOfDay();

        $totalRevenue = DB::table('orders')
            ->whereBetween('created_at', [$from, $to])
            ->sum('total_amount');

        $customersOrdered = DB::table('orders')
            ->whereBetween('created_at', [$from, $to])
            ->distinct('customer_id')
            ->count('customer_id');

        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->whereBetween('orders.created_at', [$from, $to])
            ->select(
                'products.uuid',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.uuid', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return response()->json([
            'total_revenue' => (float) $totalRevenue,
            'customers_ordered' => $customersOrdered,
            'top_products' => $topProducts,
            'date_range' => [
                'from' => $request->input('from'),
                'to' => $request->input('to'),
            ],
        ]);
    }
}
