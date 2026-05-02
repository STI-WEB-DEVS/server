<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Product;
use App\Repository\OrderItemRepository;

class OrderService
{
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;

    public function __construct(OrderRepository $orderRepository, OrderItemRepository $orderItemRepository) 
    {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    public function createOrder(array $payload)
    {
        $user = auth()->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    $customer = $user->customer;   // This uses the relationship you already have

    if (!$customer) {
        return response()->json(['message' => 'No customer linked to this user account'], 422);
    }
        // $customer = \App\Models\Customer::where('uuid', $payload['customer_uuid'])->firstOrFail();

    $totalAmount = 0;

    $order = $this->orderRepository->create([
        'customer_id' => $customer->id,
        'total_amount' => 0
    ]);

    foreach ($payload['items'] as $item) {

        $product = \App\Models\Product::where('uuid', $item['product_uuid'])->firstOrFail();

        $lineTotal = $product->price * $item['quantity'];

        $totalAmount += $lineTotal;

        $this->orderItemRepository->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $item['quantity'],
            'unit_price' => $product->price
        ]);
    }

    $order->update([
    'total_amount' => $totalAmount
]);

return new \App\Http\Resources\OrderResource($order->fresh());
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

    public function getOrdersByCustomer(string $customerUuid)
{
    $customer = Customer::where('uuid', $customerUuid)->firstOrFail();

    $orders = $this->orderRepository->getOrdersByCustomer($customer->id);

    return OrderResource::collection($orders);
}
}