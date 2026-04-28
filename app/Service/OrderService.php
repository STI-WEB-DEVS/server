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
    private CustomerRepository $customerRepository;
    private ProductRepository $productRepository;


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

    public function createOrder(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            // 1. Find Customer ID using the Repository
            $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);

            // 2. Initialize Order through the OrderRepository
            $order = $this->orderRepository->create([
                'customer_id' => $customer->id,
                'total_amount' => 0,
            ]);

            $runningTotal = 0;

            // 3. Process Multiple Products (Payload Design requirement)
            foreach ($payload['items'] as $item) {
                $product = $this->productRepository->findByUuid($item['product_uuid']);
                $lineTotal = $product->price * $item['quantity'];
                $runningTotal += $lineTotal;

                // Create individual items
                $this->orderRepository->createItem($order, [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                ]);
            }

            // 4. Update the final total via Repository
            $this->orderRepository->update($order->uuid, ['total_amount' => $runningTotal]);

            return new OrderResource($order->refresh()->load('items'));
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
    public function listOrdersByCustomer(string $customerUuid)
    {
        $orders = $this->orderRepository->getByCustomerUuid($customerUuid);
        return OrderResource::collection($orders); 
    }
}