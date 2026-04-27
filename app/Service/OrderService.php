<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderService
{
    private OrderRepository $orderRepository;
    private CustomerRepository $customerRepository;
    private ProductRepository $productRepository;

    public function __construct(
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        ProductRepository $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    /**
     * Create an order with items
     * 
     * Payload format:
     * {
     *     "customer_uuid": "uuid-string",
     *     "items": [
     *         {
     *             "product_uuid": "uuid-string",
     *             "quantity": 5
     *         }
     *     ]
     * }
     */
    public function createOrder(array $payload)
    {
        // Get customer by UUID
        $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);
        
        // Calculate total amount
        $totalAmount = 0;
        $items = [];
        
        foreach ($payload['items'] as $item) {
            $product = $this->productRepository->findByUuid($item['product_uuid']);
            $subtotal = $product->price * $item['quantity'];
            $totalAmount += $subtotal;
            
            $items[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
            ];
        }
        
        // Create order
        $order = Order::create([
            'customer_id' => $customer->id,
            'total_amount' => $totalAmount,
        ]);
        
        // Create order items
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                ...$item,
            ]);
        }
        
        // Reload with relationships
        $order->load(['items.product', 'customer']);
        
        return new OrderResource($order);
    }

    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);
        return new OrderResource($model);
    }

    public function getOrderByField(string $field, $value)
    {
        $model = $this->orderRepository->findByField($field, $value);
        return new OrderResource($model);
    }

    /**
     * Get all orders for a customer by customer UUID
     */
    public function getOrdersByCustomer(string $customerUuid, int $perPage = 15)
    {
        $orders = $this->orderRepository->getOrdersByCustomerUuid($customerUuid, $perPage);
        return OrderResource::collection($orders);
    }

    public function updateOrder(string $uuid, array $payload)
    {
        $model = $this->orderRepository->update($uuid, $payload);
        return new OrderResource($model);
    }

    public function deleteOrder(string $uuid)
    {
        $this->orderRepository->delete($uuid);
        return true;
    }

    public function restoreOrder(string $uuid)
    {
        $model = $this->orderRepository->restore($uuid);
        return new OrderResource($model);
    }
}