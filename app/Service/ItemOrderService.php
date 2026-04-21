<?php

namespace App\Service;

use App\Repository\ItemOrderRepository;
use App\Http\Resources\ItemOrderResource;

class ItemOrderService
{
    private ItemOrderRepository $itemOrderRepository;

    public function __construct(ItemOrderRepository $itemOrderRepository) 
    {
        $this->itemOrderRepository = $itemOrderRepository;
    }

    public function listItemOrder(int $perPage = 15)
    {
        $collection = $this->itemOrderRepository->paginate($perPage);
        return ItemOrderResource::collection($collection);
    }

    public function createItemOrder(array $payload)
    {
        $model = $this->itemOrderRepository->create($payload);
        return new ItemOrderResource($model);
    }

    public function getItemOrder(string $uuid)
    {
        $model = $this->itemOrderRepository->findByUuid($uuid);
        return new ItemOrderResource($model);
    }

    public function getItemOrderByField(string $field, $value)
    {
        $model = $this->itemOrderRepository->findByField($field, $value);
        return new ItemOrderResource($model);
    }

    public function updateItemOrder(string $uuid, array $payload)
    {
        $model = $this->itemOrderRepository->update($uuid, $payload);
        return new ItemOrderResource($model);
    }

    public function deleteItemOrder(string $uuid)
    {
        $this->itemOrderRepository->delete($uuid);
        return true;
    }

    public function restoreItemOrder(string $uuid)
    {
        $model = $this->itemOrderRepository->restore($uuid);
        return new ItemOrderResource($model);
    }
}