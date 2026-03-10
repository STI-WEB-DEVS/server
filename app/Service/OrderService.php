<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderItemRepository;
use App\Http\Resources\OrderResource;

class OrderService
{
    private OrderRepository $orderRepository;
    private CustomerRepository $customerRepository;
    private ProductRepository $productRepository;
    private OrderItemRepository $orderItemRepository;

    public function __construct(
        OrderRepository $orderRepository, 
        CustomerRepository $customerRepository, 
        ProductRepository $productRepository,
        OrderItemRepository $orderItemRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    public function listCustomerOrders(string $customerUuid, int $perPage = 15)
    {
        $customer = $this->customerRepository->findByUuid($customerUuid);
        $collection = $this->orderRepository->paginateByField('customer_id', $customer->id, $perPage);
        return OrderResource::collection($collection);
    }

    public function createOrder(array $payload)
    {
        $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);

        $totalAmount = 0;
        $orderItemsData = [];

        // Calculate total amount and prepare order items
        foreach ($payload['products'] as $productItem) {
            $product = $this->productRepository->findByUuid($productItem['product_uuid']);
            
            $quantity = $productItem['quantity'];
            $price = $product->price;
            
            $totalAmount += ($price * $quantity);
            
            $orderItemsData[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $price,
            ];
        }

        $order = $this->orderRepository->create([
            'customer_id' => $customer->id,
            'total_amount' => $totalAmount,
        ]);

        foreach ($orderItemsData as $itemData) {
            $itemData['order_id'] = $order->id;
            $this->orderItemRepository->create($itemData);
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

    public function restoreOrder(string $uuid)
    {
        $model = $this->orderRepository->restore($uuid);
        return new OrderResource($model);
    }
}