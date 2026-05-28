<?php

namespace App\Service;

use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

use App\Repository\OrdersRepository;
use App\Http\Resources\OrdersResource;
use App\Repository\CustomersRepository;
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

    /**
     * CREATE ORDER
     */
    public function createOrders(array $payload)
    {
        return DB::transaction(function () use ($payload) {

            // VALIDATION
            if (
                !isset($payload['customer_uuid']) ||
                !isset($payload['items'])
            ) {
                throw new \InvalidArgumentException(
                    'Invalid payload.'
                );
            }

            // FIND CUSTOMER
            $customerUuid = $payload['customer_uuid'];

            $customer = $this->customerRepository
                ->findByUuid($customerUuid);

            // CREATE ORDER
            $order = $this->orderRepository->create([
                'customer_id' => $customer->id,
                'total_amount' => 0,
            ]);

            $total = 0;

            // LOOP ITEMS
            foreach ($payload['items'] as $item) {

                // VALIDATE ITEM
                if (
                    !isset($item['product_uuid']) ||
                    !isset($item['quantity'])
                ) {
                    throw new \InvalidArgumentException(
                        'Each item must contain product_uuid and quantity.'
                    );
                }

                // FIND PRODUCT
                $productUuid = $item['product_uuid'];

                $product = $this->productRepository
                    ->findByUuid($productUuid);

                // ITEM DETAILS
                $quantity = $item['quantity'];

                $unitPrice = $product->price;

                $subtotal = $unitPrice * $quantity;

                $total += $subtotal;

                // CREATE ORDER ITEM
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ]);
            }

            // UPDATE TOTAL
            $order->update([
                'total_amount' => $total
            ]);

            // LOAD ITEMS + PRODUCT
            $order->load([
                'items.product'
            ]);

            return new OrdersResource($order);
        });
    }

    /**
     * LIST ALL ORDERS
     */
    public function listOrders(int $perPage = 15)
    {
        $collection = $this->orderRepository
            ->paginate($perPage);

        return OrdersResource::collection($collection);
    }

    /**
     * GET CUSTOMER ORDERS
     */
    public function getOrders(string $uuid)
    {
        // FIND CUSTOMER
        $model = $this->customerRepository
            ->findByUuid($uuid);

        // LOAD ORDERS WITH ITEMS + PRODUCT
        $orders = $model->orders()
            ->with([
                'items.product'
            ])
            ->latest()
            ->get();

        return OrdersResource::collection($orders);
    }

    /**
     * GET SINGLE ORDER
     */
    public function getOrderByField(
        string $field,
        $value
    ) {
        $model = $this->orderRepository
            ->findByField($field, $value);

        $model->load([
            'items.product'
        ]);

        return new OrdersResource($model);
    }

    /**
     * UPDATE ORDER
     */
    public function updateOrders(
        string $uuid,
        array $payload
    ) {
        $model = $this->orderRepository
            ->update($uuid, $payload);

        return new OrdersResource($model);
    }

    /**
     * DELETE ORDER
     */
    public function deleteOrders(string $uuid)
    {
        $this->orderRepository->delete($uuid);

        return true;
    }

    /**
     * RESTORE ORDER
     */
    public function restoreOrders(string $uuid)
    {
        $model = $this->orderRepository
            ->restore($uuid);

        return new OrdersResource($model);
    }
}

