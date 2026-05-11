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
        //get product id and customer also product amount
        //generate oders total amount
        $order = [
            "customer_id" => $this->customerRepository->findByUuid($payload['customer_uuid'])['id'],
            "total_amount" => 0
        ];

        $orderItem = [];
        foreach($payload['orders'] as $items){
            $product = $this->productRepository->findByUuid($items['product_uuid']);
            $order['total_amount'] += $product['price'] * $items['product_quantity'];
            $orderItem[] = [
                "product_id" => $product['id'],
                "quantity" => $items['product_quantity'],
                "unit_price" => $product['price']
            ];
        };

        $model = $this->orderRepository->create($order, $orderItem);
        return new OrderResource($model);
    }

    public function getOrder(string $uuid)
    {
        $customer = $this->customerRepository->findByUuid($uuid);
        $model = $this->orderRepository->findByCustomerUuid($customer['id']);
        return $model;
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