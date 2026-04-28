<?php

namespace App\Service;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

use App\Repository\OrdersRepository;
use App\Http\Resources\OrdersResource;
use App\Repository\CustomersRepository; // Fix plural
use App\Repository\ProductsRepository;
class OrdersService
{
    private CustomersRepository $customerRepository;
    private OrdersRepository $orderRepository;
    private ProductsRepository $productRepository;

    public function __construct(
        OrdersRepository $orderRepository, 
        CustomersRepository $customerRepository,
        ProductsRepository $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
    }
    public function createOrders(array $payload)
    {
    return DB::transaction(function () use ($payload) {

        if (!isset($payload['customer_uuid']) || !isset($payload['items'])) {
            throw new \InvalidArgumentException('Invalid payload.');
        }   
        $customerUuid = $payload['customer_uuid'];
        $customer = $this->customerRepository->findByUuid($customerUuid);

        $order = $this->orderRepository->create([
            'customer_id' => $customer->id,
            'total_amount' => 0,
        ]);

        $total = 0;

        foreach ($payload['items'] as $item) {

            $productUuid = $item['product_uuid'];
            $product = $this->productRepository->findByUuid($productUuid);
            $quantity = $item['quantity'];
            $unitPrice = $product->price;

            $subtotal = $unitPrice * $quantity;
            $total += $subtotal;

            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]);
        }

        $order->update([
            'total_amount' => $total
        ]);

        return new OrdersResource($order->load('items'));
    });
}

    public function listOrders(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrdersResource::collection($collection);
    }


    public function getOrders(string $uuid)
    {
        $model = $this->customerRepository->findByUuid($uuid);
        $orders = $model->orders()->with('items')->latest()->get();
        return OrdersResource::collection($orders);
    }

    public function getOrderByField(string $field, $value)
    {
        $model = $this->orderRepository->findByField($field, $value);
        return new OrdersResource($model);
    }

    public function updateOrders(string $uuid, array $payload)
    {
        $model = $this->orderRepository->update($uuid, $payload);
        return new OrdersResource($model);
    }

    public function deleteOrders(string $uuid)
    {
        $this->orderRepository->delete($uuid);
        return true;
    }

    public function restoreOrders(string $uuid)
    {
        $model = $this->orderRepository->restore($uuid);
        return new OrdersResource($model);
    }
}