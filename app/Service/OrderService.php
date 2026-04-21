<?php

namespace App\Service;

use App\Http\Resources\OrderResource;
use App\Repository\CustomerRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private CustomerRepository $customerRepository,
        private ProductRepository $productRepository,
    ) {
    }

    public function createOrder(array $payload): OrderResource
    {
        $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);

        $order = DB::transaction(function () use ($customer, $payload) {
            $resolvedItems = [];
            $totalAmount = 0;

            foreach ($payload['items'] as $item) {
                $product = $this->productRepository->findByUuid($item['product_uuid']);
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $product->price;

                $resolvedItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ];

                $totalAmount += ($unitPrice * $quantity);
            }

            $order = $this->orderRepository->create([
                'customer_id' => $customer->id,
                'total_amount' => $totalAmount,
            ]);

            foreach ($resolvedItems as $resolvedItem) {
                $this->orderRepository->createItem($order, $resolvedItem);
            }

            return $order->load(['customer', 'items.product']);
        });

        return new OrderResource($order);
    }

    public function listByCustomer(string $customerUuid): array
    {
        $customer = $this->customerRepository->findByUuid($customerUuid);
        $orders = $this->orderRepository->listByCustomerId($customer->id);

        return $orders
            ->map(fn ($order) => (new OrderResource($order))->toArray(request()))
            ->values()
            ->all();
    }
}
