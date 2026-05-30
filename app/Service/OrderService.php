<?php

namespace App\Service;

use App\Models\Customer;
use App\Models\Product;
use App\Repository\OrderRepository;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    public function createOrder(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            $customer = null;

            if (!empty($payload['customer_uuid'])) {
                $customer = Customer::where('uuid', $payload['customer_uuid'])->first();
            }

            $user = auth()->user();

            if (!$customer && $user) {
                $customer = $user->customer;
            }

            if (!$customer && $user) {
                $customer = Customer::firstOrCreate(
                    ['email' => $user->email],
                    ['name'  => $user->name]
                );

                if (!$user->customer_id) {
                    $user->update(['customer_id' => $customer->id]);
                }
            }

            if (!$customer) {
                abort(422, 'Unable to resolve customer for order');
            }

            // ── Step 1: validate ALL stock before touching anything ──────────
            foreach ($payload['items'] as $item) {
                $product = Product::where('uuid', $item['product_uuid'])->lockForUpdate()->firstOrFail();

                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'stock' => "Insufficient stock for \"{$product->name}\". "
                                 . "Available: {$product->stock}, requested: {$item['quantity']}.",
                    ]);
                }
            }

            // ── Step 2: create the order ─────────────────────────────────────
            $order = $this->orderRepository->create([
                'customer_id'  => $customer->id,
                'total_amount' => 0,
            ]);

            // ── Step 3: create items and deduct stock ────────────────────────
            $total = 0;
            foreach ($payload['items'] as $item) {
                $product   = Product::where('uuid', $item['product_uuid'])->lockForUpdate()->firstOrFail();
                $unitPrice = $product->price;
                $quantity  = $item['quantity'];

                // Deduct stock
                $product->decrement('stock', $quantity);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                $total += $unitPrice * $quantity;
            }

            $order->update(['total_amount' => $total]);
            $order->load(['customer', 'items.product']);

            return new OrderResource($order);
        });
    }

    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);
        return new OrderResource($model);
    }

    public function listOrdersByCustomer(string $customerUuid, int $perPage = 15)
    {
        $collection = $this->orderRepository->findByCustomerUuid($customerUuid, $perPage);
        return OrderResource::collection($collection);
    }

    public function deleteOrder(string $uuid)
    {
        $this->orderRepository->delete($uuid);
        return true;
    }

    public function getSummary(array $filters = [])
    {
        return $this->orderRepository->getSummary($filters);
    }
}