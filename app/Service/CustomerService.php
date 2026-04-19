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

        // Fixed: Use CustomerResource::collection
        return CustomerResource::collection($collection);
    }

    public function createCustomer(array $payload)
    {
        $model = $this->customerRepository->create($payload);

        // Fixed: Wrap the model in a Resource, not the Repository
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