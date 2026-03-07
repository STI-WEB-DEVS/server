<?php

namespace App\Service;

use App\Repository\OrderListRepository;
use App\Http\Resources\OrderListResource;

class OrderListService
{
    private OrderListRepository $orderListRepository;

    public function __construct(OrderListRepository $orderListRepository) 
    {
        $this->orderListRepository = $orderListRepository;
    }

    public function listOrderList(int $perPage = 15)
    {
        $collection = $this->orderListRepository->paginate($perPage);
        return OrderListResource::collection($collection);
    }

    public function createOrderList(array $payload)
    {
        $model = $this->orderListRepository->create($payload);
        return new OrderListResource($model);
    }

    public function getOrderList(string $uuid)
    {
        $model = $this->orderListRepository->findByUuid($uuid);
        return new OrderListResource($model);
    }

    public function getOrderListByField(string $field, $value)
    {
        $model = $this->orderListRepository->findByField($field, $value);
        return new OrderListResource($model);
    }

    public function updateOrderList(string $uuid, array $payload)
    {
        $model = $this->orderListRepository->update($uuid, $payload);
        return new OrderListResource($model);
    }

    public function deleteOrderList(string $uuid)
    {
        $this->orderListRepository->delete($uuid);
        return true;
    }

    public function restoreOrderList(string $uuid)
    {
        $model = $this->orderListRepository->restore($uuid);
        return new OrderListResource($model);
    }
}