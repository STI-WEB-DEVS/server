<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderItemRepository;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\DB;
use App\Models\Customer; // Ensure this is imported

class OrderService
{
    private OrderRepository $orderRepository;
    private ProductRepository $productRepository;
    private OrderItemRepository $orderItemRepository;

    public function __construct(
        OrderRepository $orderRepository,
        ProductRepository $productRepository,
        OrderItemRepository $orderItemRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. CONVERT: Find the customer record using the input UUID
            $customer = \App\Models\Customer::where('uuid', $data['customer_uuid'])->firstOrFail();

            $totalAmount = 0;
            $itemsToProcess = [];

            foreach ($data['items'] as $item) {
                // 2. CONVERT: Find product by UUID to get its numeric ID and current price
                $product = \App\Models\Product::where('uuid', $item['product_uuid'])->firstOrFail();
                
                $totalAmount += ($product->price * $item['quantity']);

                $itemsToProcess[] = [
                    'product_id' => $product->id, // We save the ID
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price
                ];
            }

            // 3. SAVE: Store the numeric customer_id in the orders table
            $order = $this->orderRepository->create([
                'customer_id'  => $customer->id, // Integer saved here
                'total_amount' => $totalAmount,
                'status'       => 'pending'
            ]);

            // 4. SAVE ITEMS: Using numeric product_id
            foreach ($itemsToProcess as $itemData) {
                $itemData['order_id'] = $order->id;
                $this->orderItemRepository->create($itemData);
            }

            return $order->load(['items.product', 'customer']);
        });
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

    public function getOrdersByCustomer(string $customerUuid)
    {
        $orders = $this->orderRepository->findByCustomerUuid($customerUuid);
        return OrderResource::collection($orders);
    }
}