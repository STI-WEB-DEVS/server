<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Models\Customer;
use App\Models\Product;

class OrderService
{
    protected OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function createOrder(array $data)
    {
        $customer = Customer::where('uuid', $data['customer_uuid'])->firstOrFail();
        $product  = Product::where('uuid', $data['product_uuid'])->firstOrFail();

        $orderData = [
            'customer_id'  => $customer->id,
            'total_amount' => $product->price * $data['quantity'],
        ];

        $itemData = [
            'product_id' => $product->id,
            'quantity'   => $data['quantity'],
            'unit_price' => $product->price,
        ];

        return $this->orderRepository->createWithItem($orderData, $itemData);
    }

    public function getOrdersByCustomer(string $uuid)
    {
        return $this->orderRepository->getByCustomerUuid($uuid);
    }
}