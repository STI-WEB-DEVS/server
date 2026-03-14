<?php

namespace App\Service;

use App\Http\Resources\OrderItemResource;
use App\Repository\OrderItemRepository;

class OrderItemService
{
    private OrderItemRepository $orderItemRepository;

    public function __construct(OrderItemRepository $orderItemRepository)
    {
        $this->orderItemRepository = $orderItemRepository;
    }

    public function listOrderItem(int $perPage = 15)
    {
        $collection = $this->orderItemRepository->paginate($perPage);

        return OrderItemResource::collection($collection);
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
