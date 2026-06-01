<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\OrderItemsRepository;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use App\Http\Resources\OrderResource;
use App\Repository\ItemOrderRepository;

class OrderService
{
    private OrderRepository $orderRepository;
    private ItemOrderRepository $itemOrderRepository;
    private ProductRepository $productRepository;
    private CustomerRepository $customerRepository;

    public function __construct(
        OrderRepository $orderRepository, 
        ItemOrderRepository $itemOrderRepository, 
        ProductRepository $productRepository,
        CustomerRepository $customerRepository
    ) {
        $this->orderRepository      = $orderRepository;
        $this->itemOrderRepository  = $itemOrderRepository;
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

    /**
     * Creates an order safely using repository patterns and session data.
     */
    /**
     * Creates an order safely, supporting both single and multiple item collections.
     */
 /**
     * Creates an order safely, supporting multiple line items from the authenticated user.
     */
    public function createOrder(string $customerUuid, array $items)
    {
        // 1. Identify the authenticated customer directly via their account link column
        $customerId = auth()->user()->customer_id;

        if (!$customerId) {
            throw new \Exception("The authenticated user account does not have an attached customer profile.");
        }

        // 2. Loop through items to verify each product exists and compile the grand total
        $total = 0;
        foreach ($items as $item) {
            $product = $this->productRepository->findByUuid($item['product_uuid']);
            if (!$product) {
                throw new \Exception("Product not found for UUID: " . $item['product_uuid']);
            }
            $total += $product->price * $item['quantity'];
        }

        // 3. Create the parent order (HasUuids trait on the Order model auto-generates the 'uuid' string column)
        $order = $this->orderRepository->create([
            'customer_id'  => $customerId,
            'total_amount' => $total,
        ]);

        // 4. Save each line item safely into your order_items database table
        foreach ($items as $item) {
            $product = $this->productRepository->findByUuid($item['product_uuid']);
            
            $this->orderItemRepository->create([
                'order_id'   => $order->id, // Links directly to the auto-increment integer id
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'unit_price' => $product->price,
            ]);
        }

        return new \App\Http\Resources\OrderResource($order);
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