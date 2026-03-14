<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Http\Resources\OrderResource;

class OrderService
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    public function createOrder(array $payload)
    {
        $model = $this->orderRepository->create($payload);
        return new OrderResource($model);
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

    /**
     * Output #2 — Create order with items
     */
    public function createOrderWithItems(array $payload): OrderResource
    {
        $order = $this->orderRepository->createWithItems(
            $payload['customer_uuid'],
            $payload['items']
        );

        return new OrderResource($order);
    }

    /**
     * Output #3 — Get all orders by customer uuid
     */
    public function getOrdersByCustomer(string $customerUuid): mixed
    {
        $orders = $this->orderRepository->getByCustomerUuid($customerUuid);
        return OrderResource::collection($orders);
    }
}