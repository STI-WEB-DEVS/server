<?php

namespace App\Service;

use App\Http\Resources\OrderResource;
use App\Repository\OrderRepository;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;


use Illuminate\Support\Str;
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
    /**
     * FIX: Added this method to resolve the "Undefined method" error.
     * This calls the repository to get all records.
     */
    public function getAllOrders()
    {
        $collection = $this->orderRepository->all();
        return OrderResource::collection($collection);
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);

        return OrderResource::collection($collection);
    }

   
    public function createOrder(array $payload)
    {
        $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);
        
        return DB::transaction(function () use ($customer, $payload) {
            $order = $this->orderRepository->create([
                'uuid'         => (string) Str::uuid(),
                'customer_id'  => $customer->id,
                'total_amount' => 0, 
            ]);

            $totalAmount = 0;

            foreach ($payload['items'] as $itemData) {
                $product = $this->productRepository->findByUuid($itemData['product_uuid']);
                
                $subtotal = $product->price * $itemData['quantity'];
                $totalAmount += $subtotal;

                $this->orderRepository->createItem([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $itemData['quantity'],
                    'unit_price' => $product->price,
                ]);
            }

            // Update the order with the final calculated total
            $order->update(['total_amount' => $totalAmount]);

            return new OrderResource($order->load('items.product'));
        });
    }

    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);

        return new OrderResource($model);
    }

    /**
     * Requirement: Return a list of orders made by a customer.
     */
    public function getOrdersByCustomer(string $customerUuid)
    {
        $orders = $this->orderRepository->findByCustomerUuid($customerUuid);
        return OrderResource::collection($orders);
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