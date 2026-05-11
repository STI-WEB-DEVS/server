<?php

namespace App\Service;

use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Http\Resources\OrderResource;

class OrderService
{
    private ProductRepository $productRepository;
    private CustomerRepository $customerRepository;
    private OrderRepository $orderRepository;

    public function __construct(ProductRepository $productRepository, CustomerRepository $customerRepository, OrderRepository $orderRepository)
    {
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
   ==========================================     
    private ProductRepository $CustomerRepository;
    private CustomerRepository $ProductRepository;
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository, ProductRepository $CustomerRepository, CustomerRepository $ProductRepository)
>>>>>>> de78a7229f941edd7596b479bd0c8203cab984fb
    {
        $this->productRepository = $productRepository;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->CustomerRepository = $CustomerRepository;
        $this->ProductRepository = $ProductRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    public function createOrder(array $payload)
    {
        $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);

        $totalAmount = 0;
        foreach ($payload['orders'] as $order) {
            $product = $this->productRepository->findByUuid($order['product_uuid']);
            $totalAmount += $product->price * $order['product_quantity'];
        }

        $createdOrder = $this->orderRepository->create([
            'customer_id' => $customer->id,
            'total_amount' => $totalAmount,
        ]);

        foreach ($payload['orders'] as $order) {
            $product = $this->ProductRepository->findByUuid($payload['product_uuid']);
            //$product = Product::where('uuid', $order['product_uuid'])->first();
            $createdOrder->items()->create([
                'product_id' => $product->id,
                'quantity' => $order['product_quantity'],
                'unit_price' => $product->price,
            ]);
        }

        return new OrderResource($createdOrder->load('items'));
    }

    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);
        return new OrderResource($model);
    }

    public function getOrdersByCustomerUuid(string $customerUuid, int $perPage = 15)
    {
        $customer = $this->CustomerRepository->findByUuid($customerUuid);
        //$customer = Customer::where('uuid', $customerUuid)->firstOrFail();
        $orders = $this->orderRepository->getByCustomerId($customer->id, $perPage);
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
}

