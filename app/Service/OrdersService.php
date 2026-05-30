<?php

namespace App\Service;

use App\Http\Resources\OrderSummaryResource;
use App\Http\Resources\OrdersResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Repository\OrdersRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class OrdersService
{
    private OrdersRepository $ordersRepository;

    public function __construct(OrdersRepository $ordersRepository)
    {
        $this->ordersRepository = $ordersRepository;
    }

    public function listOrders(int $perPage = 15)
    {
        $collection = $this->ordersRepository->paginate($perPage);
        return OrdersResource::collection($collection);
    }

    public function listOrdersByCustomerUuid(string $customerUuid, int $perPage = 15)
    {
        $customer = Customer::where('uuid', $customerUuid)->firstOrFail();

        $collection = $this->ordersRepository->paginateByCustomerId($customer->id, $perPage);

        return [
            'orders' => $collection->total(),
            'data' => OrdersResource::collection($collection),
            'links' => [
                'first' => $collection->url(1),
                'last' => $collection->url($collection->lastPage()),
                'prev' => $collection->previousPageUrl(),
                'next' => $collection->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $collection->currentPage(),
                'from' => $collection->firstItem(),
                'last_page' => $collection->lastPage(),
                'to' => $collection->lastItem(),
                'per_page' => $collection->perPage(),
                'total' => $collection->total(),
            ]
        ];
    }

    public function createOrders(array $payload)
    {
        $data = collect($payload);

        if (! $data->has('customer_uuid') || ! $data->has('items')) {
            return response()->json(['message' => 'customer_uuid and items are required'], 422);
        }

        $order = DB::transaction(function () use ($data) {
            $customer = Customer::where('uuid', $data['customer_uuid'])->firstOrFail();

            $totalAmount = 0;

            $order = Order::create([
                'customer_id' => $customer->id,
                'total_amount' => 0,
            ]);

            foreach ($data['items'] as $item) {
                $orderedQuantity = (int) ($item['quantity'] ?? 0);
                $product = Product::where('uuid', $item['product_uuid'])->lockForUpdate()->firstOrFail();

                if ($orderedQuantity <= 0) {
                    throw new HttpResponseException(response()->json([
                        'message' => "Invalid quantity for product {$product->name}.",
                    ], 422));
                }

                if ((int) $product->quantity < $orderedQuantity) {
                    throw new HttpResponseException(response()->json([
                        'message' => "Insufficient stock for product {$product->name}.",
                    ], 422));
                }

                $lineTotal = $product->price * $orderedQuantity;
                $totalAmount += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $orderedQuantity,
                    'unit_price' => $product->price,
                ]);

                $product->decrement('quantity', $orderedQuantity);
            }

            $order->update([
                'total_amount' => $totalAmount,
            ]);

            return $order;
        });

        return $order->load(['customer', 'orderItems.product']);
    }

    public function getOrders(string $uuid)
    {
        $model = $this->ordersRepository->findByUuid($uuid);
        return new OrdersResource($model);
    }

    public function getOrdersByField(string $field, $value)
    {
        $model = $this->ordersRepository->findByField($field, $value);
        return new OrdersResource($model);
    }

    public function updateOrders(string $uuid, array $payload)
    {
        $model = $this->ordersRepository->update($uuid, $payload);
        return new OrdersResource($model);
    }

    public function deleteOrders(string $uuid)
    {
        $this->ordersRepository->delete($uuid);
        return true;
    }

    public function restoreOrders(string $uuid)
    {
        $model = $this->ordersRepository->restore($uuid);
        return new OrdersResource($model);
    }

    public function getOrderSummary(?string $from, ?string $to)
    {
        $from = $from ?? now()->startOfMonth()->toDateString();
        $to = $to ?? now()->toDateString();

        $summary = $this->ordersRepository->getSummary($from, $to);

        return new OrderSummaryResource($summary);
    }
}
