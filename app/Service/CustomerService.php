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
        // This returns a LengthAwarePaginator
        $collection = $this->customerRepository->paginate($perPage);
        
        // This wraps the array in "data" and adds "meta" automatically
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

    public function updateCustomer(string $uuid, array $payload)
    {
        $model = $this->customerRepository->update($uuid, $payload);
        return new CustomerResource($model);
    }

    public function deleteCustomer(string $uuid)
    {
        return $this->customerRepository->delete($uuid);
    }

    public function restoreCustomer(string $uuid)
    {
        $model = $this->customerRepository->restore($uuid);
        return new CustomerResource($model);
    }

    public function getCustomerWithOrders(string $uuid): CustomerResource
    {
        $model = $this->customerRepository->getWithOrders($uuid);
        return new CustomerResource($model);
    }
}