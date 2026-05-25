<?php

namespace App\Service;

use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use App\Repository\CustomersRepository;
use App\Http\Resources\OrdersResource;

class OrderService
{
    private OrdersRepository $orderRepository;
    private ProductsRepository $productRepository;
    private CustomersRepository $customerRepository;

    public function __construct(OrdersRepository $orderRepository, ProductsRepository $productRepository, CustomersRepository $customerRepository) 
    {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrdersResource::collection($collection);
    }

    public function listOrdersByCustomer(string $customerUuid, int $perPage = 15)
    {
        $customer = $this->customerRepository->findByUuid($customerUuid);


        $collection = $this->orderRepository->paginateByCustomer($customer->id, $perPage);

        return OrdersResource::collection($collection);
    }

    public function createOrder(array $payload)
    {
        $customer_uuid = $this->customerRepository->findByUuid($payload['customer_uuid']);
        $items = $payload['items'];

        $resolvedItems = [];
        $total = 0;

        foreach($items as $item){
            $product = $this->productRepository->findByUuid($item['product_uuid']);

            
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

        return new OrdersResource($order);
    }

    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);
        return new OrdersResource($model);
    }

    public function getOrderByField(string $field, $value)
    {
        $model = $this->orderRepository->findByField($field, $value);
        return new OrdersResource($model);
    }

    public function updateOrder(string $uuid, array $payload)
    {
        $model = $this->orderRepository->update($uuid, $payload);
        return new OrdersResource($model);
    }

    public function deleteOrder(string $uuid)
    {
        $this->orderRepository->delete($uuid);
        return true;
    }

    public function restoreOrder(string $uuid)
    {
        $model = $this->orderRepository->restore($uuid);
        return new OrdersResource($model);
    }
}