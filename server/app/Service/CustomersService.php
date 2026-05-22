<?php

namespace App\Service;

use App\Models\Customer; 

use App\Repository\CustomersRepository;
use App\Http\Resources\CustomersResource;

class CustomersService
{
    private CustomersRepository $customersRepository;

    public function __construct(CustomersRepository $customersRepository) 
    {
        $this->customersRepository = $customersRepository;
    }

    public function listCustomers(int $perPage = 15)
    {
        $collection = $this->customersRepository->paginate($perPage);
        return CustomersResource::collection($collection);
    }

    public function createCustomers(array $payload)
    {
        $model = $this->customersRepository->create($payload);
        return new CustomersResource($model);
    }

    public function getCustomers(string $uuid)
    {
        $model = $this->customersRepository->findByUuid($uuid);
        return new CustomersResource($model);
    }


    public function getCustomersByField(string $field, $value)
    {
        $model = $this->customersRepository->findByField($field, $value);
        return new CustomersResource($model);
    }

    public function updateCustomers(string $uuid, array $payload)
    {
        $model = $this->customersRepository->update($uuid, $payload);
        return new CustomersResource($model);
    }

    public function deleteCustomers(string $uuid)
    {
        $this->customersRepository->delete($uuid);
        return true;
    }

    public function restoreCustomers(string $uuid)
    {
        $model = $this->customersRepository->restore($uuid);
        return new CustomersResource($model);
    }


    public function getCustomerOrders(string $uuid)
    {
        $customer = Customer::where('uuid', $uuid)->firstOrFail();

        // ✅ This is where your line goes
        $orders = $customer->orders()->with(['items.product'])->get();

        return [
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
                            'product_uuid' => $item->product->uuid,
                            'product_name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'subtotal' => $item->quantity * $item->unit_price,
                        ];
                    }),
                ];
            }),
        ];
    }

}