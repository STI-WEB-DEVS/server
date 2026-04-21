<?php

namespace App\Service;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

use App\Repository\OrderRepository;
use App\Http\Resources\OrderResource;

class OrderService
{
    public function createOrder(array $payload)
{
    return DB::transaction(function () use ($payload) {

        if (!isset($payload['customer_uuid']) || !isset($payload['items'])) {
            throw new \InvalidArgumentException('Invalid payload.');
        }

        $customer = Customer::where('uuid', $payload['customer_uuid'])->firstOrFail();

        $order = $this->orderRepository->create([
            'customer_id' => $customer->id,
            'total_amount' => 0,
        ]);

        $total = 0;

        foreach ($payload['items'] as $item) {

            $product = Product::where('uuid', $item['product_uuid'])->firstOrFail();

            $quantity = $item['quantity'];
            $unitPrice = $product->price;

            $subtotal = $unitPrice * $quantity;
            $total += $subtotal;

            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]);
        }

        $order->update([
            'total_amount' => $total
        ]);

        return new OrderResource($order->load('items'));
    });
}

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
}