<?php

namespace App\Service;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
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
        $customer = Customer::where('uuid', $payload['customer_id'])->firstOrFail();

        if ($customer) {
            $order = $this->orderRepository->create([
                'customer_id'  => $customer->id,
                'total_amount' => 0,
            ]);


            $total = 0;
            foreach ($payload['items'] as $item) {
                $product = Product::where('uuid', $item['product_id'])->firstOrFail();
                $unitPrice = $product->price;
                $quantity  = $item['quantity'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                $total += $unitPrice * $quantity;
            }

            $order->update(['total_amount' => $total]);

            // Ensure relations are loaded so the resource includes items and customer
            return new OrderResource($order->fresh(['customer', 'items.product']));
        }
            
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

    public function updateOrder(string $uuid, array $payload)
    {
        $order = $this->orderRepository->findByUuid($uuid);
        
        // Update customer if provided
        if (isset($payload['customer_id'])) {
            $customer = Customer::where('uuid', $payload['customer_id'])->firstOrFail();
            $order->update(['customer_id' => $customer->id]);
        }

        // Update items if provided
        $total = 0;
        if (isset($payload['items'])) {
            // Delete existing items
            $order->items()->delete();
            
            // Create new items
            foreach ($payload['items'] as $item) {
                $product = Product::where('uuid', $item['product_id'])->firstOrFail();
                $unitPrice = $product->price;
                $quantity  = $item['quantity'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                $total += $unitPrice * $quantity;
            }
            
            $order->update(['total_amount' => $total]);
        }

        return new OrderResource($order->fresh(['customer', 'items.product']));
    }
}
