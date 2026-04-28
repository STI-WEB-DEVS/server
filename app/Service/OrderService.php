<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Http\Resources\OrderResource;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private CustomerRepository $customerRepo;
    private ProductRepository $productRepo;
    private OrderRepository $orderRepo;

    public function __construct(
        CustomerRepository $customerRepo,
        ProductRepository $productRepo,
        OrderRepository $orderRepo
    ) {
        $this->customerRepo = $customerRepo;
        $this->productRepo = $productRepo;
        $this->orderRepo = $orderRepo;
    }

    public function listOrder(int $perPage = 15)
    {
        // Fixed property name from orderRepository to orderRepo
        $collection = $this->orderRepo->paginate($perPage);
        return OrderResource::collection($collection);
    }

    /**
     * Create a new order with multiple items.
     */
    public function createOrder(array $payload)
{
    return DB::transaction(function () use ($payload) {
        // 1. Find the customer model using the UUID from the payload
        $customer = $this->customerRepo->findByUuid($payload['customer_uuid']);
        
        $totalAmount = 0;
        $orderItems = [];

        foreach ($payload['items'] as $item) {
            // 2. Find the product model using the UUID from the payload
            $product = $this->productRepo->findByUuid($item['product_uuid']);
            
            $unitPrice = $product->price;
            $subtotal = $unitPrice * $item['quantity'];
            $totalAmount += $subtotal;

            $orderItems[] = [
                'product_id' => $product->id, // Use the INTERNAL ID here for the DB
                'quantity'   => $item['quantity'],
                'unit_price' => $unitPrice,
            ];
        }

        // 3. Create the order using the INTERNAL ID of the customer
        $order = $this->orderRepo->create([
            'customer_id'  => $customer->id, // Use $customer->id, not the UUID string
            'total_amount' => $totalAmount,
        ]);

        $order->items()->createMany($orderItems);

        return new OrderResource($order->load('items'));
    });
}

    public function getOrder(string $uuid)
    {
        $model = $this->orderRepo->findByUuid($uuid);
        return new OrderResource($model);
    }

    public function getOrderByField(string $field, $value)
    {
        $model = $this->orderRepo->findByField($field, $value);
        return new OrderResource($model);
    }

    public function updateOrder(string $uuid, array $payload)
    {
        $model = $this->orderRepo->update($uuid, $payload);
        return new OrderResource($model);
    }

    public function deleteOrder(string $uuid)
    {
        $this->orderRepo->delete($uuid);
        return true;
    }

    public function restoreOrder(string $uuid)
    {
        $model = $this->orderRepo->restore($uuid);
        return new OrderResource($model);
    }
}