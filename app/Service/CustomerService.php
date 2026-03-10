<?php

namespace App\Service;

use App\Repository\CustomerRepository;
use App\Http\Resources\CustomerResource;

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

    public function getCustomerByField(string $field, $value)
    {
        $model = $this->customerRepository->findByField($field, $value);
        return new CustomerResource($model);
    }

    public function updateCustomer(string $uuid, array $payload)
    {
        $model = $this->customerRepository->update($uuid, $payload);
        return new CustomerResource($model);
    }

    public function deleteCustomer(string $uuid)
    {
        $this->customerRepository->delete($uuid);
        return true;
    }

    public function restoreCustomer(string $uuid)
    {
        $model = $this->customerRepository->restore($uuid);
        return new CustomerResource($model);
    }
}