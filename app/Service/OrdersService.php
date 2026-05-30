<?php

namespace App\Service;

use App\Repository\OrdersRepository;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Http\Resources\OrdersResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Product;
use App\Models\Order;

class OrdersService
{
    private OrdersRepository $ordersRepository;
    private CustomerRepository $customerRepository;
    private ProductRepository $productRepository;

    public function __construct(
        OrdersRepository $ordersRepository,
        CustomerRepository $customerRepository,
        ProductRepository $productRepository
    ) {
        $this->ordersRepository    = $ordersRepository;
        $this->customerRepository  = $customerRepository;
        $this->productRepository   = $productRepository;
    }

    public function createOrders(array $payload)
    {
        return DB::transaction(function () use ($payload) {

            $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);

            if (!$customer) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Customer not found.");
            }

            // ── Stock validation (before any writes) ─────────────────
            foreach ($payload['items'] as $item) {
                $product  = Product::where('uuid', $item['product_uuid'])->firstOrFail();
                $quantity = (int) $item['quantity'];

                if ($product->stock_quantity <= 0) {
                    throw ValidationException::withMessages([
                        'items' => ["{$product->name} is out of stock."],
                    ]);
                }

                if ($quantity > $product->stock_quantity) {
                    throw ValidationException::withMessages([
                        'items' => [
                            "Not enough stock for {$product->name}. "
                            . "Requested: {$quantity}, Available: {$product->stock_quantity}."
                        ],
                    ]);
                }
            }

            // ── Create order ──────────────────────────────────────────
            $order = $this->ordersRepository->create([
                'customer_id'  => $customer->id,
                'total_amount' => 0,
                'uuid'         => (string) \Illuminate\Support\Str::uuid(),
            ]);

            $totalAmount = 0;

            foreach ($payload['items'] as $item) {
                $product   = Product::where('uuid', $item['product_uuid'])->firstOrFail();
                $unitPrice = (float) $product->price;
                $quantity  = (int) $item['quantity'];
                $subtotal  = $unitPrice * $quantity;

                $this->ordersRepository->addItem($order, [
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                // ── Decrement stock ───────────────────────────────────
                $product->decrement('stock_quantity', $quantity);

                $totalAmount += $subtotal;
            }

            $this->ordersRepository->updateTotal($order, $totalAmount);

            return new OrdersResource($order->load('items.product'));
        });
    }

    public function getCustomerOrders(string $uuid)
    {
        $customer = $this->customerRepository->findByUuid($uuid);

        if (!$customer) {
            return collect();
        }

        return Order::where('customer_id', $customer->id)
            ->with('items.product')
            ->get();
    }

    public function listOrders(int $perPage = 15)
    {
        $collection = $this->ordersRepository->paginate($perPage);
        return OrdersResource::collection($collection);
    }

    public function getOrders(string $uuid)
    {
        $model = $this->ordersRepository->findByUuid($uuid);
        return new OrdersResource($model);
    }

    public function getOrdersByField(string $field, $value)
    {
        $model = $this->ordersRepository->findByField($field, $value);
        return new OrdersResource($model);
    }

    public function updateOrders(string $uuid, array $payload)
    {
        $model = $this->ordersRepository->update($uuid, $payload);
        return new OrdersResource($model);
    }

    public function deleteOrders(string $uuid)
    {
        $this->ordersRepository->delete($uuid);
        return true;
    }

    public function restoreOrders(string $uuid)
    {
        $model = $this->ordersRepository->restore($uuid);
        return new OrdersResource($model);
    }
}