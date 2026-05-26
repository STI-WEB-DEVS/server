<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate payload fields
        $validated = $request->validate([
            'customer_id'  => 'required',
            'total_amount' => 'required|numeric',
            'items'        => 'required|array',
            'items.*.product_identifier' => 'required',
            'items.*.product_name'       => 'required|string',
            'items.*.quantity'           => 'required|integer|min:1',
            'items.*.price'              => 'required|numeric',
        ]);

        // 2. Wrap operations in a transaction for stock tracking safety
        DB::beginTransaction();
        try {
            // Save the parent order row
            $order = Order::create([
                'customer_id'  => $validated['customer_id'],
                'total_amount' => $validated['total_amount'],
                'items'        => $validated['items'] // Ensure your Order model has 'items' cast to an 'array'
            ]);

            // 3. Deduct stock quantities from inventory using matching identifier strings
            foreach ($validated['items'] as $item) {
                $product = Product::where('uuid', $item['product_identifier'])
                                  ->orWhere('id', $item['product_identifier'])
                                  ->first();

                if ($product) {
                    if ($product->stock >= $item['quantity']) {
                        $product->decrement('stock', $item['quantity']);
                    } else {
                        return response()->json(['message' => "Stock depleted for {$product->name}"], 400);
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Order processed successfully!', 'order' => $order], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Transaction processing dropped.', 'error' => $e->getMessage()], 500);
        }
    }
}