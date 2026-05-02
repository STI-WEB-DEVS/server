<?php

namespace App\Service;

use App\Models\Product;
use App\Repository\OrderRepository;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\DB;
use Exception;

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

    public function listOrdersForCustomer(int $customerId, int $perPage = 15)
    {
        $collection = $this->orderRepository->paginateForCustomer($customerId, $perPage);
        return OrderResource::collection($collection);
    }

    public function createOrder(array $payload)
    {
        $model = $this->orderRepository->create($payload);
        return new OrderResource($model);
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

    // Custom
    public function placeOrder($customer, array $items)
    {
        try {
            DB::beginTransaction();

            $order = $this->orderRepository->create([
                'customer_id' => $customer->id,
                'total_amount' => 0,
            ]);

            $totalAmount = 0;

            foreach ($items as $item) {
                $product = Product::where('uuid', $item['product_uuid'])->firstOrFail();
                
                $this->orderRepository->createItem([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ]);

                $totalAmount += $product->price * $item['quantity'];
            }

            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return new OrderResource($order->load('items'));
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getAllOrders()
    {
        return $this->orderRepository->getAll();
    }
}
