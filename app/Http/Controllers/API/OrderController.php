<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Whiteboard Requirement 1: Create Order Submission
     */
    public function store(Request $request)
    {
        // 1. Validate incoming JSON payload keys from checkout.vue
        $request->validate([
            'customer_uuid' => 'required|string|exists:customers,uuid',
            'items' => 'required|array|min:1',
            'items.*.product_uuid' => 'required|string|exists:products,uuid',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Wrap in a transaction so if one item fails, it database rollbacks cleanly
        return DB::transaction(function () use ($request) {
            
            // 2. Find internal customer ID using the global UUID sent by Nuxt
            $customer = Customer::where('uuid', $request->customer_uuid)->firstOrFail();

            // 3. Create parent transaction record
            $order = Order::create([
                'customer_id' => $customer->id,
                'total_amount' => 0, // Placeholder total, we will calculate below
            ]);

            $calculatedTotal = 0;

            // 4. Loop through array data to attach items
            foreach ($request->items as $item) {
                $product = Product::where('uuid', $item['product_uuid'])->firstOrFail();
                $subtotal = $product->price * $item['quantity'];
                $calculatedTotal += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ]);
            }

            // 5. Update parent record with final calculated price currency total
            $order->update(['total_amount' => $calculatedTotal]);

            // 6. Return response payload to satisfy your whiteboard requirement!
            return response()->json([
                'message' => 'Order created successfully!',
                'order_id' => $order->id,
                'total_amount' => $calculatedTotal,
                'customer_uuid' => $request->customer_uuid,
            ], 201);
    });
    }

    /**
     * Whiteboard Requirement 2: Display Analytics Dashboard Aggregates Within a Date Range
     */
    public function summary(Request $request)
    {
        // 1. Capture query parameters or default to trailing 30 days if left blank
        $startDate = $request->query('start_date') 
            ? Carbon::parse($request->query('start_date'))->startOfDay() 
            : Carbon::now()->subDays(30)->startOfDay();
            
        $endDate = $request->query('end_date') 
            ? Carbon::parse($request->query('end_date'))->endOfDay() 
            : Carbon::now()->endOfDay();

      $totalRevenue = DB::table('order_items')
    ->select(DB::raw('SUM(quantity * unit_price) as total'))
    ->value('total');

        // 3. Display number of customers who ordered within date range (Distinct count)
        $uniqueCustomersCount = Order::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('customer_id')
            ->count('customer_id');

        // 4. FIXED: Filter top products using parent orders table timestamp boundary markers
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id') // Joined parent orders table
            ->select(
                'products.uuid',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity_sold'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_earned')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate]) // Filter based on order date context
            ->groupBy('products.id', 'products.uuid', 'products.name')
            ->orderBy('total_quantity_sold', 'desc')
            ->limit(5)
            ->get();

        // 5. Build dynamic tracking response object payload
        return response()->json([
            'date_range' => [
                'start' => $startDate->toDateTimeString(),
                'end' => $endDate->toDateTimeString()
            ],
            'metrics' => [
                'total_revenue' => (float) $totalRevenue,
                'unique_customers' => (int) $uniqueCustomersCount
            ],
            'top_products' => $topProducts
        ], 200);
    }
}