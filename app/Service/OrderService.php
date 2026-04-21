<?php

namespace App\Service;

use App\Http\Resources\OrderResource;
use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;

class OrderService
{
    private CustomerRepository $customerRepository;
    private ProductRepository $productRepository;
    private OrderRepository $orderRepository;

    public function __construct(
        CustomerRepository $customerRepository,
        ProductRepository $productRepository,
        OrderRepository $orderRepository
    )
    {
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
    }

    public function createOrder(array $payload): OrderResource
    {
        $customer = $this->customerRepository->findByUuid($payload['customer_uuid'])['data'];

        $productUuids = collect($payload['items'])
            ->pluck('product_uuid')
            ->values();

        $products = $this->productRepository->findByUuids($productUuids->all());

        $items = collect($payload['items'])->map(function (array $item) use ($products) {
            $product = $products->get($item['product_uuid']);

            return [
                'product_id' => $product->id,
                'quantity' => (int) $item['quantity'],
                'unit_price' => (float) $product->price,
            ];
        })->values();

        $totalAmount = $items->sum(function (array $item) {
            return $item['unit_price'] * $item['quantity'];
        });

        $model = $this->orderRepository->create([
            'customer_id' => $customer->id,
            'items' => $items->all(),
            'total_amount' => $totalAmount,
        ]);

        return new OrderResource($model);
    }

    public function listOrdersByCustomer(string $customerUuid, ?int $limit = null)
    {
        $collection = $this->orderRepository->listByCustomerUuid($customerUuid, $limit);

        return OrderResource::collection($collection);
    }
}