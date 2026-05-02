<?php

namespace App\Service;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
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
}
