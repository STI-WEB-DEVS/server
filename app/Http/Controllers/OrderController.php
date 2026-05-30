<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $customerUuid = $request->query('customer_uuid');
        $customerId = $request->query('customer_id');

        $orders = Order::with(['items.product', 'customer']);

        if ($customerId) {
            $orders->where('customer_id', $customerId);
        } elseif ($customerUuid) {
            $orders->whereHas('customer', function ($query) use ($customerUuid) {
                $query->where('uuid', $customerUuid);
            });
        }

        $paginated = $orders->latest()->paginate(15);

        return response()->json([
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'last_page' => $paginated->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required_without:customer_uuid|integer|exists:customers,id',
            'customer_uuid' => 'required_without:customer_id|uuid|exists:customers,uuid',
            'total_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_uuid' => 'required|string|exists:products,uuid',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $order = DB::transaction(function () use ($validated) {
            $customer = isset($validated['customer_id'])
                ? Customer::findOrFail($validated['customer_id'])
                : Customer::where('uuid', $validated['customer_uuid'])->firstOrFail();

            $order = Order::create([
                'customer_id' => $customer->id,
                'total_amount' => $validated['total_amount'],
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::where('uuid', $item['product_uuid'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($product->stock_quantity < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => ["Insufficient stock available for {$product->name}."],
                    ]);
                }

                $product->decrement('stock_quantity', $item['quantity']);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            return $order->load(['items.product', 'customer']);
        });

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order,
            'customer_uuid' => $order->customer?->uuid,
        ], 201);
    }
}
