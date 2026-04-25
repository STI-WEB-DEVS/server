<?php

namespace App\Services;

use App\Repository\OrderRepository;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Http\Resources\OrderResource;

class OrderService
{
    private OrderRepository $orderRepository;
    private CustomerRepository $customerRepository;
    private ProductRepository $productRepository;

    // I-inject ang lahat ng kailangang repositories
    public function __construct(
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        ProductRepository $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Requirement: Output #2 - Create Order with multiple items
     */
    public function createOrder(array $payload)
    {
        // GINAMIT NA ANG REPOSITORY: Hindi na Model direkta
        $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);
        
        $totalAmount = 0;
        $preparedItems = [];

        foreach ($payload['items'] as $item) {
            // GINAMIT NA ANG REPOSITORY
            $product = $this->productRepository->findByUuid($item['product_uuid']);
            
            $subtotal = $product->price * $item['quantity'];
            $totalAmount += $subtotal;

            $preparedItems[] = [
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'unit_price' => $product->price,
            ];
        }

        $order = $this->orderRepository->createWithItems(
            $customer->id, 
            $totalAmount, 
            $preparedItems
        );

        return new OrderResource($order);
    }

    public function getCustomerOrders(string $customerUuid)
    {
        // GINAMIT NA ANG REPOSITORY
        $customer = $this->customerRepository->findByUuid($customerUuid);
        
        $collection = $this->orderRepository->getByCustomerId($customer->id);
        
        return OrderResource::collection($collection);
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);
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