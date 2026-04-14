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

    public function getAllCustomers()
    {
        return CustomerResource::all();
    }

    public function createCustomer(array $data)
    {
        return CustomerResource::create($data);
    }

    public function getCustomer(string $uuid)
    {
        $model = $this->customerRepository->findByUuid($uuid);

        return new CustomerResource($model);    }

    public function updateCustomer($uuid, array $payload)
    {
        $model = $this->customerRepository->update($uuid, $payload);

        return new CustomerResource($model);
    }

    public function deleteCustomer($uuid)
    {
        $this->customerRepository->delete($uuid);

        return true;
    }
}