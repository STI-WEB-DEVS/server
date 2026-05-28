<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Repository\OrderRepository;
use App\Repository\CustomersRepository as CustomerRepository;
use App\Repository\ProductsRepository  as ProductRepository;
use App\Http\Resources\OrdersResource  as OrderResource;

class OrderService
{
    private CustomerRepository $customerRepository;
    private OrderRepository    $orderRepository;
    private ProductRepository  $productRepository;

    public function __construct(
        OrderRepository    $orderRepository,
        CustomerRepository $customerRepository,
        ProductRepository  $productRepository
    ) {
        $this->orderRepository    = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->productRepository  = $productRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginateWithDetails($perPage);
        return OrderResource::collection($collection);
    }

    public function createOrder(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);

            $order = $this->orderRepository->create([
                'customer_id'  => $customer->id,
                'total_amount' => 0,
            ]);

            $total = 0;

            foreach ($payload['items'] as $item) {
                $product  = $this->productRepository->findByUuid($item['product_uuid']);
                $subtotal = $product->price * $item['quantity'];
                $total   += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                ]);
            }

            $order->update(['total_amount' => $total]);

            return new OrderResource($order->load('items.product', 'customer'));
        });
    }

    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);
        return new OrderResource($model->load('items.product', 'customer'));
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

    public function listCustomerOrders(User $user)
    {
        if (! $user->customer_id) {
            return response()->json(['data' => []]);
        }

        $orders = $this->orderRepository->getByCustomerId($user->customer_id);
        return OrderResource::collection($orders);
    }
}
