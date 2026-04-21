<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Http\Resources\OrderResource;

use Illuminate\Support\Facades\DB;

class OrderService
{
    private OrderRepository $orderRepository;

    public function __construct(
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        ProductRepository $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    public function listCustomerOrders(string $customerUuid, int $perPage = 15)
    {
        // Reuse the customer repository to find the internal ID
        $customer = $this->customerRepository->findByUuid($customerUuid);
        
        $orders = $this->orderRepository->findByCustomerId($customer->id, $perPage);
        
        return OrderResource::collection($orders);
    }

    public function createOrder(array $payload)
    {
        // Wrap everything in a database transaction to ensure data integrity
        return DB::transaction(function () use ($payload) {
            
            // 1. Fetch the customer using their UUID
            $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);

            $totalAmount = 0;
            $itemsToCreate = [];

            // 2. Process each item from the request
            foreach ($payload['items'] as $item) {
                // Fetch the product to get the reliable database price
                $product = $this->productRepository->findByUuid($item['product_uuid']);
                
                $quantity = $item['quantity'];
                $unitPrice = $product->price;

                // Calculate subtotal and add to the main total
                $totalAmount += ($unitPrice * $quantity);

                // Prepare the item array (using standard internal IDs for foreign keys)
                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                ];
            }

            // 3. Create the parent Order
            $order = $this->orderRepository->create([
                'customer_id'  => $customer->id,
                'total_amount' => $totalAmount,
            ]);

            // 4. Create the associated Order Items using Eloquent relationships
            foreach ($itemsToCreate as $itemData) {
                $order->items()->create($itemData);
            }

            // 5. Load the items relation before returning the resource
            $order->load('items');

            return new OrderResource($order);
        });
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