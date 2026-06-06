<?php

namespace App\Service;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Repository\OrderRepository;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Http\Resources\OrderResource;
use Illuminate\Validation\ValidationException;

class OrderService
{
    private CustomerRepository $customerRepository;
    private OrderRepository $orderRepository;
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

                if ($quantity < 1) {
                    throw ValidationException::withMessages([
                        'items' => 'Quantity must be at least 1.',
                    ]);
                }

                if ($quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'items' => "Only {$product->stock} stock available for {$product->name}.",
                    ]);
                }

                $unitPrice = $product->price;

                $subtotal = $unitPrice * $quantity;
                $total += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                $product->decrement('stock', $quantity);
            }

            $order->update([
                'total_amount' => $total
            ]);

            return new OrderResource($order->load(['customer', 'items.product']));
        });
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
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
        if (isset($payload['items'])) {
            return DB::transaction(function () use ($uuid, $payload) {
                $order = $this->orderRepository->findByUuid($uuid);

                if (isset($payload['customer_uuid'])) {
                    $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);
                    $order->update(['customer_id' => $customer->id]);
                }

                $order->items()->delete();

                $total = 0;

                foreach ($payload['items'] as $item) {
                    $product = $this->productRepository->findByUuid($item['product_uuid']);
                    $quantity = $item['quantity'];

                    if ($quantity < 1) {
                        throw ValidationException::withMessages([
                            'items' => 'Quantity must be at least 1.',
                        ]);
                    }

                    if ($quantity > $product->stock) {
                        throw ValidationException::withMessages([
                            'items' => "Only {$product->stock} stock available for {$product->name}.",
                        ]);
                    }

                    $unitPrice = $product->price;
                    $total += $unitPrice * $quantity;

                    $order->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                    ]);

                    $product->decrement('stock', $quantity);
                }

                $order->update(['total_amount' => $total]);

                return new OrderResource($order->load(['customer', 'items.product']));
            });
        }

        if (isset($payload['customer_uuid'])) {
            $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);
            $model = $this->orderRepository->update($uuid, ['customer_id' => $customer->id]);
            return new OrderResource($model->load(['customer', 'items.product']));
        }

        $model = $this->orderRepository->findByUuid($uuid);
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

    public function getOrdersByCustomer(string $customerUuid)
    {
        $customer = Customer::where('uuid', $customerUuid)->firstOrFail();
        
        $orders = Order::where('customer_id', $customer->id)
                       ->with(['customer', 'items.product'])
                       ->latest()
                       ->paginate();

        return OrderResource::collection($orders);
    }
}
