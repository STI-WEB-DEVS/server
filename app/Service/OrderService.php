<?php

namespace App\Service;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {

            $customer = Customer::where('uuid', $data['customer_uuid'])->firstOrFail();

            $order = Order::create([
                'customer_id' => $customer->id,
                'total_amount' => 0,
            ]);


            $total = 0;

            foreach ($data['items'] as $item) {

                $product = Product::where('uuid', $item['product_uuid'])->firstOrFail();

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ]);
            }

            $order->update([
                'total_amount' => $total,
            ]);

            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order->load('items')
            ]);
        });
    }

    public function getSummary(array $filters)
    {
        $from = isset($filters['from']) && $filters['from'] ? Carbon::parse($filters['from'])->startOfDay() : Carbon::parse('1970-01-01')->startOfDay();
        $to = isset($filters['to']) && $filters['to'] ? Carbon::parse($filters['to'])->endOfDay() : Carbon::now()->endOfDay();

        $orders = Order::whereBetween('created_at', [$from, $to]);

        $totalAmount = $orders->sum('total_amount');
        $customerCount = $orders->distinct('customer_id')->count('customer_id');

        $topProducts = OrderItem::select(
            'products.uuid as product_uuid',
            'products.name as product_name',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
        )
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->whereBetween('orders.created_at', [$from, $to])
        ->groupBy('products.uuid', 'products.name')
        ->orderByDesc('total_quantity')
        ->limit(5)
        ->get();

        return response()->json([
            'data' => [
                'total_amount' => $totalAmount,
                'customer_count' => $customerCount,
                'top_products' => $topProducts,
                'range' => [
                    'from' => $from->toDateString(),
                    'to' => $to->toDateString(),
                ],
            ]
        ]);
    }
}
