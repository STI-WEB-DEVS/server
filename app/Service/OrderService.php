<?php

namespace App\Service;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
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
                $product  = Product::where('uuid', $item['product_id'])->firstOrFail();
                $quantity = $item['quantity'];

                // ✅ Check stock before proceeding
                if ($product->stock_quantity < $quantity) {
                    throw new \Exception(
                        "Insufficient stock for \"{$product->name}\". " .
                        "Available: {$product->stock_quantity}, Requested: {$quantity}."
                    );
                }

                $unitPrice = $product->price;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                // ✅ Deduct stock
                $product->decrement('stock_quantity', $quantity);

                $total += $unitPrice * $quantity;
            }

            $order->update(['total_amount' => $total]);

            return new OrderResource($order->load(['customer', 'items.product']));
        });
    }

    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);
        return new OrderResource($model);
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

    public function getOrderSummary(?string $from, ?string $to): array
    {
        return $this->orderRepository->getSummary($from, $to);
    }
}