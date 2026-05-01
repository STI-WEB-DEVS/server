<?php

namespace App\Service;

use App\Models\Customer;
use App\Repository\CustomerRepository;

class CustomerService
{
    private customerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function createCustomer($payload)
    {
        return $this->customerRepository->createCustomer($payload);
    }

    public function retrieveCustomer($payload)
    {
        return $this->customerRepository->retrieveCustomer($payload);
    }

    public function updateCustomer($payload, $id)
    {
        return $this->customerRepository->updateCustomer($payload, $id);
    }
    public function deleteCustomer($payload)
    {
        return $this->customerRepository->deleteCustomer($payload);
    }

    public function getCustomers()
    {
        return $this->customerRepository->getAllCustomers();
    }

    public function getCustomerOrders(string $customer_uuid)
    {

        $customer = $this->customerRepository->getCustomerOrders($customer_uuid);

        if (!$customer) {
            return response()->json([
                'message' => 'Customer Not Found.'
            ], 404);
        };
        $orders = $customer->orders;


        return response()->json([
            'customer' => [
                'uuid' => $customer->uuid,
                'name' => $customer->name,
                'email' => $customer->email,
            ],
            'orders' => $orders->map(function ($order) {
                return [
                    'uuid' => $order->uuid,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product' => [
                                'uuid' => $item->product->uuid,
                                'name' => $item->product->name,
                            ],
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'subtotal' => $item->quantity * $item->unit_price,
                        ];
                    }),
                ];
            }),
        ]);
    }
}
