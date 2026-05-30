<?php

namespace App\Service;

use App\Models\Customer;
use App\Models\Product;
use App\Repository\OrderRepository;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    public function createOrder(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            $customer = Customer::where('uuid', $payload['customer_uuid'])->firstOrFail();
    
            $order = $this->orderRepository->create([
                'customer_id'  => $customer->id,
                'total_amount' => 0,
            ]);
    
            $total = 0;
            foreach ($payload['items'] as $item) {
                // Lock the row to prevent race conditions
                $product = Product::where('uuid', $item['product_uuid'])
                    ->lockForUpdate()
                    ->firstOrFail();
    
                $quantity = $item['quantity'];
    
                if ($product->stock_quantity < $quantity) {
                    throw new \Exception(
                        "Insufficient stock for \"{$product->name}\". Available: {$product->stock_quantity}."
                    );
                }
    
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $product->price,
                ]);
    
                // Deduct stock
                $product->decrement('stock_quantity', $quantity);
    
                $total += $product->price * $quantity;
            }
    
            $order->update(['total_amount' => $total]);
            $order->load(['customer', 'items.product']);
    
            return new OrderResource($order);
        });
    }
    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);
        return new OrderResource($model);
    }

    public function listOrdersByCustomer(string $customerUuid, int $perPage = 15)
    {
        $collection = $this->orderRepository->findByCustomerUuid($customerUuid, $perPage);
        return OrderResource::collection($collection);
    }

    public function deleteOrder(string $uuid)
    {
        $this->orderRepository->delete($uuid);
        return true;
    }

    public function getSummary(string $from, string $to): array
    {
        return [
            'total_revenue'    => $this->orderRepository->getRevenueByDateRange($from, $to),
            'total_orders'     => $this->orderRepository->getOrderCountByDateRange($from, $to),
            'customer_count'   => $this->orderRepository->getCustomerCountByDateRange($from, $to),
            'top_products'     => $this->orderRepository->getTopProductsByDateRange($from, $to),
        ];
    }
}
