<?php

namespace App\Service;

use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Http\Resources\OrderItemResource;

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

    public function getOrderItemsByCustomer(string $uuid)
    {
        $customer_id = $this->customerRepository->findByUuid($uuid)->id;
        $order_ids = $this->orderRepository->getByCustomerId($customer_id)->pluck('id')->toArray();
        $items = $this->orderItemRepository->getByOrderIds($order_ids);
        
        $grouped = $items->groupBy('order_id')->map(function ($orderItems, $orderId) {
            return [
                'order_id' => $orderId,
                'items' => OrderItemResource::collection($orderItems),
                'order_total' => $orderItems->sum(function ($item) {
                    return $item->quantity * $item->unit_price;
                })
            ];
        })->values();

        return ['data' => $grouped];
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