<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Http\Resources\OrderResource;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository) 
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * List paginated orders.
     */
    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    /**
     * Create a new order record.
     */
    public function createOrder(array $payload)
    {
        $model = $this->orderRepository->create($payload);
        return new OrderResource($model);
    }

    /**
     * Retrieve a single order by UUID.
     */
    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);
        return new OrderResource($model);
    }

    /**
     * Retrieve an order by a specific field (e.g., status or reference).
     */
    public function getOrderByField(string $field, $value)
    {
        $model = $this->orderRepository->findByField($field, $value);
        return new OrderResource($model);
    }

    /**
     * Update order details.
     */
    public function updateOrder(string $uuid, array $payload)
    {
        $model = $this->orderRepository->update($uuid, $payload);
        return new OrderResource($model);
    }

    /**
     * Delete an order.
     */
    public function deleteOrder(string $uuid)
    {
        $this->orderRepository->delete($uuid);
        return true;
    }

    /**
     * Restore a soft-deleted order.
     */
    public function restoreOrder(string $uuid)
    {
        $model = $this->orderRepository->restore($uuid);
        return new OrderResource($model);
    }
}