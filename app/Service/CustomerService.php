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

    /**
     * Get a paginated list of customers.
     */
    public function listCustomers(int $perPage = 15)
    {
        $collection = $this->customerRepository->paginate($perPage);

        return CustomerResource::collection($collection);
    }

    /**
     * Create a new customer record.
     */
    public function createCustomer(array $payload)
    {
        $model = $this->customerRepository->create($payload);

        return new CustomerResource($model);
    }

    /**
     * Find a customer by their UUID.
     */
    public function getCustomer(string $uuid)
    {
        $model = $this->customerRepository->findByUuid($uuid);

        return new CustomerResource($model);
    }

    /**
     * Find a customer by a specific field/value pair.
     */
    public function getCustomerByField(string $field, $value)
    {
        $model = $this->customerRepository->findByField($field, $value);

        return new CustomerResource($model);
    }

    /**
     * Update an existing customer.
     */
    public function updateCustomer(string $uuid, array $payload)
    {
        $model = $this->customerRepository->update($uuid, $payload);

        return new CustomerResource($model);
    }

    /**
     * Soft/Hard delete a customer.
     */
    public function deleteCustomer(string $uuid)
    {
        $this->customerRepository->delete($uuid);

        return true;
    }

    /**
     * Restore a deleted customer.
     */
    public function restoreCustomer(string $uuid)
    {
        $model = $this->customerRepository->restore($uuid);

        return new CustomerResource($model);
    }
}