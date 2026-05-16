<?php

namespace App\Service;

use App\Models\Customer;
use App\Models\Product;
use App\Repository\OrderRepository;
use App\Http\Resources\OrderResource;
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

    public function createOrder(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            $customer = Customer::where('uuid', $payload['customer_uuid'])->firstOrFail();

            $order = $this->orderRepository->create([
                'customer_id'  => $customer->id,
                'total_amount' => 0,
            ]);

            $total = 0;
            foreach ($payload['items'] as $item) {
                $product = Product::where('uuid', $item['product_uuid'])->firstOrFail();
                $unitPrice = $product->price;
                $quantity  = $item['quantity'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                $total += $unitPrice * $quantity;
            }

            $order->update(['total_amount' => $total]);
            $order->load(['customer', 'items.product']);

            return new OrderResource($order);
        });
    }

    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);
        return new OrderResource($model);
    }

    public function getOrderSummary(string $from, string $to)
    {
        // Set the time explicitly to cover the entire start and end days
        $from = $from . ' 00:00:00';
        $to = $to . ' 23:59:59';

        return $this->orderRepository->getSummary($from, $to);
    }

    public function listOrdersByCustomer(string $customerUuid, int $perPage = 15)
    {
        $collection = $this->orderRepository->findByCustomerUuid($customerUuid, $perPage);
        return OrderResource::collection($collection);
    }

    public function deleteOrder(string $uuid)
    {
        $this->orderRepository->delete($uuid);
        return true;
    }
}