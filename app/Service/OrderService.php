<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use App\Service\DB;
use App\Http\Resources\OrderResource;
use App\Models\Customer;

class OrderService
{
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private CustomerRepository $customerRepository;

    public function __construct(OrderRepository $orderRepository, ProductRepository $productRepository, CustomerRepository $customerRepository) 
    {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;

    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    public function createOrder(array $payload)
   {
        // Fetch the customer using object syntax
        $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);
        
        $orderData = [
            "customer_id"  => $customer->id,
            "total_amount" => 0
        ];

        $orderItemsData = [];
        
        // Calculate the total dynamically based on actual database prices
        foreach ($payload['orders'] as $item) {
            $product = $this->productRepository->findByUuid($item['product_uuid']);
            
            $orderData['total_amount'] += ($product->price * $item['product_quantity']);
            
            $orderItemsData[] = [
                "product_id" => $product->id,
                "quantity"   => $item['product_quantity'],
                "unit_price" => $product->price 
            ];
        }

        $model = $this->orderRepository->create($orderData, $orderItemsData);
        
        // Load items so they appear in the API response
        $model->load('items');
        
        return new OrderResource($model);
    }

    public function getOrder(string $uuid)
   {
        $customer = $this->customerRepository->findByUuid($uuid);
        $collection = $this->orderRepository->findByCustomerUuid($customer->id);
        
        return OrderResource::collection($collection);
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