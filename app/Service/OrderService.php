<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\CustomersRepository;
use App\Repository\OrderItemRepository;
use App\Repository\ProductsRepository;
use App\Http\Resources\OrderResource;

class OrderService
{
    private OrderRepository $orderRepository;
    private CustomersRepository $customersRepository;
    private OrderItemRepository $orderItemRepository;
    private ProductsRepository $productsRepository;

    public function __construct(
        OrderRepository $orderRepository,
        CustomersRepository $customersRepository,
        OrderItemRepository $orderItemRepository,
        ProductsRepository $productsRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->customersRepository = $customersRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productsRepository = $productsRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    public function createOrder(array $payload)
    {
        // Validate customer exists
        $customer = $this->customersRepository->findByUuid($payload['customer_uuid']);

        // Compute total amount from items
        $totalAmount = 0;
        $items = [];

        foreach ($payload['items'] as $item) {
            $product = $this->productsRepository->findByUuid($item['product_uuid']);

            $totalAmount += $product->price * $item['quantity'];

            $items[] = [
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'unit_price' => $product->price,
            ];
        }

        // Create the order with computed total
        $order = $this->orderRepository->create([
            'customer_id'  => $customer->id,
            'total_amount' => $totalAmount,
        ]);

        // Create each order item
        foreach ($items as $item) {
            $this->orderItemRepository->create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);
        }

        return new OrderResource($order);
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

    public function getOrdersByCustomer(string $uuid)
    {
        $customer = $this->customersRepository->findByUuid($uuid);
        $orders = $this->orderRepository->findByCustomerId($customer->id);
        return OrderResource::collection($orders);
    }

    public function updateOrder(string $uuid, array $payload)
    {
        // Get existing order
        $order = $this->orderRepository->findByUuid($uuid);

        $totalAmount = 0;
        $items = [];

        if (!empty($payload['items'])) {
            // Delete old order items
            foreach ($order->items as $existingItem) {
                $this->orderItemRepository->delete($existingItem->uuid);
            }

            // Recompute with new items
            foreach ($payload['items'] as $item) {
                $product = $this->productsRepository->findByUuid($item['product_uuid']);

                $totalAmount += $product->price * $item['quantity'];

                $items[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                ];
            }

            // Recreate order items
            foreach ($items as $item) {
                $this->orderItemRepository->create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }
        }

        // Update order total
        $updatedOrder = $this->orderRepository->update($uuid, [
            'customer_id'  => $order->customer_id,
            'total_amount' => $totalAmount ?: $order->total_amount,
        ]);

        return new OrderResource($updatedOrder);
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
}