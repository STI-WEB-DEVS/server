<?php

namespace App\Service;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\OrderItem;
use App\Repository\OrdersRepository;
use App\Http\Resources\OrdersResource;

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
        // return OrdersResource::collection($collection);

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
        // Find customer by UUID, but use numeric ID for DB
        $customer = Customer::where('uuid', $payload['customer_uuid'])->firstOrFail();
    
        // Create order linked to customer (using numeric ID)
        $order = Order::create([
            'customer_id' => $customer->id,
        ]);
    
        // Attach products by resolving UUIDs to IDs
        foreach ($payload['items'] as $item) {
            $product = Product::where('uuid', $item['product_uuid'])->firstOrFail();
    
            OrderItem::create([
                'order_id'   => $order->id,        // ✅ numeric ID
                'product_id' => $product->id,      // ✅ numeric ID
                'quantity'   => $item['quantity'],
                'unit_price' => $product->price,
            ]);
        }
    
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
}
