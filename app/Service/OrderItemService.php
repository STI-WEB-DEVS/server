<?php

namespace App\Service;

use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\OrderResource;

class OrderItemService
{
    private OrderItemRepository $orderItemRepository;
    private OrderRepository $orderRepository;
    private \App\Repository\CustomerRepository $customerRepository;

    public function __construct(OrderItemRepository $orderItemRepository, OrderRepository $orderRepository, \App\Repository\CustomerRepository $customerRepository)
    {
        $this->orderItemRepository = $orderItemRepository;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
    }

    public function listOrderItem(int $perPage = 15)
    {
        $paginator = $this->orderRepository->paginateWithRelations($perPage);
        return OrderResource::collection($paginator);
    }

    public function createOrderItem(array $payload)
    {
        $model = $this->orderItemRepository->create($payload);
        return new OrderItemResource($model);
    }

    public function getOrderItem(string $uuid)
    {
        $model = $this->orderItemRepository->findByUuid($uuid);
        return new OrderItemResource($model);
    }

    public function getOrderItemsByCustomer(string $uuid)
    {
        $customer_id = $this->customerRepository->findByUuid($uuid)->id;
        $orders = $this->orderRepository->getByCustomerId($customer_id);

        return OrderResource::collection($orders);
    }

    public function getOrderItemByField(string $field, $value)
    {
        $model = $this->orderItemRepository->findByField($field, $value);
        return new OrderItemResource($model);
    }

    public function updateOrderItem(string $uuid, array $payload)
    {
        $model = $this->orderItemRepository->update($uuid, $payload);
        return new OrderItemResource($model);
    }

    public function deleteOrderItem(string $uuid)
    {
        $this->orderItemRepository->delete($uuid);
        return true;
    }

    public function restoreOrderItem(string $uuid)
    {
        $model = $this->orderItemRepository->restore($uuid);
        return new OrderItemResource($model);
    }
}
