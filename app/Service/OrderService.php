<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\OrderItemRepository;
use App\Repository\ProductRepository;
use App\Repository\CustomerRepository;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private OrderRepository     $orderRepository;
    private OrderItemRepository $orderItemRepository;
    private ProductRepository   $productRepository;
    private CustomerRepository  $customerRepository;

    public function __construct(
        OrderRepository     $orderRepository,
        OrderItemRepository $orderItemRepository,
        ProductRepository   $productRepository,
        CustomerRepository  $customerRepository
    ) {
        $this->orderRepository     = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->productRepository   = $productRepository;
        $this->customerRepository  = $customerRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);

        return OrderResource::collection($collection);
    }

    public function getOrdersByCustomerUuid(string $customerUuid, int $perPage = 15)
    {
        $orders = $this->orderRepository->findByCustomerUuid($customerUuid, $perPage);

        return OrderResource::collection($orders);
    }

    /**
     * Create an order using uuid-based payload.
     *
     * @param  string $customerUuid
     * @param  array  $items  Each item: ['product_uuid' => string, 'quantity' => int]
     */
    public function createOrder(string $customerUuid, array $items)
    {
        // Resolve customer via uuid
        $customer = $this->customerRepository->findByUuid($customerUuid);

        // Resolve all products first and calculate total
        $resolvedItems = [];
        $total         = 0;

        foreach ($items as $item) {
            $product = $this->productRepository->findByUuid($item['product_uuid']);

            $subtotal = $product->price * $item['quantity'];
            $total   += $subtotal;

            $resolvedItems[] = [
                'product'   => $product,
                'quantity'  => $item['quantity'],
                'unit_price' => $product->price,
                'subtotal'  => $subtotal,
            ];
        }

        // Wrap in a transaction so order + items are atomic
        $order = DB::transaction(function () use ($customer, $resolvedItems, $total) {
            $order = $this->orderRepository->create([
                'customer_id'  => $customer->id,
                'total_amount' => $total,
            ]);

            foreach ($resolvedItems as $item) {
                $this->orderItemRepository->create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            return $order;
        });

        // Reload with relationships for the resource
        $order->load(['items.product', 'customer']);

        return new OrderResource($order);
    }

    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);

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
}