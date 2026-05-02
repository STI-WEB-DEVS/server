<?php

namespace App\Service;

use App\Repository\CustomersRepository;
use App\Repository\ProductsRepository;
use App\Repository\OrdersRepository;
use App\Http\Resources\OrdersResource;

class OrdersService
{
    private OrdersRepository $ordersRepository;

    public function __construct(OrdersRepository $ordersRepository,CustomersRepository $customerRepository,ProductsRepository $productRepository) 
    {
        $this->ordersRepository = $ordersRepository;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
    }

    public function listOrders(int $perPage = 15)
    {
        $collection = $this->ordersRepository->paginate($perPage);
        return OrdersResource::collection($collection);
    }

    public function createOrders(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            // Resolve customer using the repository
            $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);
       

            $totalOrderAmount = 0;
            $itemsToCreate = [];

            // Resolve products and calculate totals
            foreach ($payload['items'] as $item) {
                $product = $this->productRepository->findByUuid($item['product_uuid']);
               

                $lineTotal = $product->price * $item['quantity'];
                $totalOrderAmount += $lineTotal;

                $itemsToCreate[] = [
                    'product_id'   => $product->id,
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $product->price,
                ];
            }

            // Create the main Order via Repository
            $order = $this->ordersRepository->create([
                'customer_id'  => $customer->id,
                'total_amount' => $totalOrderAmount,
            ]);

            // Create Order Items via Repository
            foreach ($itemsToCreate as $itemData) {
                $itemData['order_id'] = $order->id;
                $this->ordersRepository->createItem($itemData);
            }

            return new OrdersResource($order->load('items.product'));
        });
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
    public function getCustomerOrders(string $uuid)
    {
        $customer = Customer::where('uuid', $uuid)->firstOrFail();

        // ✅ This is where your line goes
        $orders = $customer->orders()->with(['items.product'])->get();

        return [
            'customer' => [
                'uuid' => $customer->uuid,
                'name' => $customer->name,
                'email' => $customer->email,
            ],
            'orders' => $orders->map(function ($order) {
                return [
                    'uuid' => $order->uuid,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_uuid' => $item->product->uuid,
                            'product_name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'subtotal' => $item->quantity * $item->unit_price,
                        ];
                    }),
                ];
            }),
        ];
    }
}
