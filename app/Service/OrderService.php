<?php

namespace App\Service;

use App\Repository\OrderRepository;
use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;

class OrderService
{
    protected OrderRepository $orderRepository;
    protected CustomerRepository $customerRepository;
    protected ProductRepository $productRepository;

    public function __construct(
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        ProductRepository $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
    }

    public function createOrder(array $data)
    {
        $customer = $this->customerRepository->findByUuid($data['customer_uuid']);
        $product  = $this->productRepository->findByUuid($data['product_uuid']);

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

    public function getOrder(string $uuid)
    {
        return $this->orderRepository->findByUuid($uuid);
    }

    public function getOrdersByCustomer(string $uuid)
    {
        return $this->orderRepository->getByCustomerUuid($uuid);
    }
}