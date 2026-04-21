<?php

namespace App\Service;

use App\Repository\OrderItemsRepository;
use App\Http\Resources\OrderResource;

class OrderItemsService
{
    private OrderItemsRepository $orderRepository;

    public function __construct(OrderItemsRepository $orderItemsRepository) 
    {
        $this->orderRepository = $orderItemsRepository;
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

    public function deleteOrder(string $uuid): bool
    {
        return $this->orderRepository->delete($uuid);
    }

    public function restoreOrder(string $uuid)
    {
        $model = $this->orderRepository->restore($uuid);
        return new OrderResource($model);
    }
}
