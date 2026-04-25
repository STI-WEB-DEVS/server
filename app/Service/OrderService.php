<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\OrderItemRepository;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use App\Http\Resources\OrderResource;

class OrderService
{
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;
    private ProductRepository $productRepository;
    private CustomerRepository $customerRepository;

    public function __construct(
        OrderRepository $orderRepository, 
        OrderItemRepository $orderItemRepository, 
        ProductRepository $productRepository,
        CustomerRepository $customerRepository
        ) 
    {
        $this->orderRepository      = $orderRepository;
        $this->orderItemRepository  = $orderItemRepository;
        $this->productRepository    = $productRepository;
        $this->customerRepository   = $customerRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    public function getOrdersByCustomerUuid(string $customerUuid, int $perPage = 15)
    {
        $orders = $this->orderRepository->findByCustomerUuid($customerUuid, $perPage);
        return OrderResource::collection($orders);
    }



    public function createOrder(int $customerId, array $items)
    {
        // Step 1: Verify customer exists
        $customer = $this->customerRepository->findById($customerId);
        if (!$customer) {
            throw new \Exception("Customer not found");
        }

        // Step 2: Calculate total
        $total = 0;
        foreach ($items as $item) {
            $product = $this->productRepository->findById($item['product_id']);
            if (!$product) {
                throw new \Exception("Product not found");
            }
            $total += $product->price * $item['quantity'];
        }

        // Step 3: Create order
        $order = $this->orderRepository->create([
            'customer_id'   => $customer->id,
            'total_amount'  => $total,
        ]);

        // Step 4: Create order items
        foreach ($items as $item) {
            $product = $this->productRepository->findById($item['product_id']);

            $this->orderItemRepository->create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'unit_price' => $product->price,
            ]);
    }

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
}