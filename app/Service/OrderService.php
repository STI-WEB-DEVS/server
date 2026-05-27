<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use App\Http\Resources\OrderResource;

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

    public function listOrdersByCustomer(string $customerUuid, int $perPage = 15)
    {
        $customer = $this->customerRepository->findByUuid($customerUuid);

        // return $customer->id;

        $collection = $this->orderRepository->paginateByCustomer($customer->id, $perPage);

        return OrderResource::collection($collection);
    }

    public function createOrder(array $payload)
    {
        $customer_uuid = $this->customerRepository->findByUuid($payload['customer_uuid']);
        $items = $payload['items'];

        $resolvedItems = [];
        $total = 0;

        foreach($items as $item){
            $product = $this->productRepository->findByUuid($item['product_uuid']);

            // return $product->all();
            
            $subtotal = $product->price * $item['quantity'];
            $total += $subtotal;

            $resolvedItems[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
            ];
        }

        $orderData = [
            'customer_id' => $customer_uuid->id,
            'total_amount' => $total,
        ];

        $order = $this->orderRepository->createWithItems($orderData, $resolvedItems);

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