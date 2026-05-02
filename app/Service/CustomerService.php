<?php

namespace App\Service;

use App\Http\Resources\CustomerResource;
use App\Repository\CustomerRepository;

class CustomerService
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function listCustomer(int $perPage = 15)
    {
        $collection = $this->customerRepository->paginate($perPage);

        return CustomerResource::collection($collection);
    }

    public function createCustomer(array $payload)
    {
        $model = $this->customerRepository->create($payload);

        return new CustomerResource($model);
    }

    public function getCustomer(string $uuid)
    {
        $model = $this->customerRepository->findByUuid($uuid);

        return new CustomerResource($model);
    }

    public function getCompanyByField(string $field, $value)
    {
        $model = $this->customerRepository->findByField($field, $value);

        return new CustomerResource($model);
    }

    public function updateCustomer(array $payload,string $uuid) 
    {
        $model = $this->customerRepository->update($uuid, $payload);

        return new CustomerResource($model);
    }

    public function paguinate(){



        return $this->customerRepository->paguinate();

    }
    

    public function deleteCustomer(string $uuid)
    {
        $this->customerRepository->delete($uuid);

        return true;
    }

    public function restoreCustomert(string $uuid)
    {
        $model = $this->customerRepository->restore($uuid);

        return new CustomerResource($model);
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
