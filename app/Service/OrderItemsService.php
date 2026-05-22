<?php

namespace App\Service;

use App\Repository\OrderItemsRepository;
use App\Http\Resources\OrderItemsResource;

class OrderItemsService
{
    private OrderItemsRepository $orderItemsRepository;

    public function __construct(OrderItemsRepository $orderItemsRepository) 
    {
        $this->orderItemsRepository = $orderItemsRepository;
    }

    public function listOrderItems(int $perPage = 15)
    {
        $collection = $this->orderItemsRepository->paginate($perPage);
        return OrderItemsResource::collection($collection);
    }

    public function createOrderItems(array $payload)
    {
        $model = $this->orderItemsRepository->create($payload);
        return new OrderItemsResource($model);
    }

    public function getOrderItems(string $uuid)
    {
        $model = $this->orderItemsRepository->findByUuid($uuid);
        return new OrderItemsResource($model);
    }

    public function getOrderItemsByField(string $field, $value)
    {
        $model = $this->orderItemsRepository->findByField($field, $value);
        return new OrderItemsResource($model);
    }

    public function updateOrderItems(string $uuid, array $payload)
    {
        $model = $this->orderItemsRepository->update($uuid, $payload);
        return new OrderItemsResource($model);
    }

    public function deleteOrderItems(string $uuid)
    {
        $this->orderItemsRepository->delete($uuid);
        return true;
    }

    public function restoreOrderItems(string $uuid)
    {
        $model = $this->orderItemsRepository->restore($uuid);
        return new OrderItemsResource($model);
    }
}