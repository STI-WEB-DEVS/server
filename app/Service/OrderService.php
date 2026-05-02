<?php

 

namespace App\Service;

use App\Models\Product;

use App\Models\Customer;

use Illuminate\Support\Facades\DB;

 

use App\Repository\OrderRepository;

use App\Http\Resources\OrderResource;

use App\Repository\CustomerRepository;

use App\Repository\ProductRepository;

 

class OrderService

{

    private CustomerRepository $customerRepository;

    private OrderRepository $orderRepository;

    private ProductRepository $productRepository;

    public function __construct(OrderRepository $orderRepository, CustomerRepository $customerRepository,

    ProductRepository $productRepository) 

    {

        $this->orderRepository = $orderRepository;

        $this->customerRepository = $customerRepository;

        $this->productRepository = $productRepository;

    }

    public function createOrder(array $payload)

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

 

        return new OrderResource($order->load('items'));

    });

}

 

    public function listOrder(int $perPage = 15)

    {

        $collection = $this->orderRepository->paginate($perPage);

        return OrderResource::collection($collection);

    }

 

 

    public function getOrder(string $uuid)

    {

        $model = $this->customerRepository->findByUuid($uuid);

        $orders = $model->orders()->with('items')->latest()->get();

        return OrderResource::collection($orders);

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