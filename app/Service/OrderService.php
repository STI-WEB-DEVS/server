<?php

namespace App\Service;

use App\Http\Resources\OrderResource;
use App\Repository\OrderRepository;
use Illuminate\Support\Facades\DB;

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

    /**
     * Requirement: Create an Order and OrderItem automatically.
     */
    public function createOrder(array $payload)
    {
        $customer = \App\Models\Customer::where('uuid', $payload['customer_uuid'])->firstOrFail();
        $product = \App\Models\Product::where('uuid', $payload['product_uuid'])->firstOrFail();
        $quantity = (int) $payload['quantity'];

        return DB::transaction(function () use ($customer, $product, $quantity) {
            $order = $this->orderRepository->create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'customer_id' => $customer->id,
                'total_amount' => $product->price * $quantity,
            ]);

            $this->orderRepository->createItem([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
            ]);

            return $order;
        });
    }

    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);

        return new OrderResource($model);
    }

    /**
     * Requirement: Return a list of orders made by a customer.
     */
    public function getOrdersByCustomer(string $customerUuid)
    {
        $orders = $this->orderRepository->findByCustomerUuid($customerUuid);

        return OrderResource::collection($orders);
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
}
