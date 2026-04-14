<?php

namespace App\Service;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Repository\CustomerRepository;

class CustomerService
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * GET: List customers with pagination using CustomerResource
     */
    public function listCustomer(int $perPage = 15)
    {
        $collection = $this->customerRepository->getAll($perPage);

        return CustomerResource::collection($collection);
    }

    /**
     * POST: Create customer
     */
    public function createCustomer(array $payload)
    {
        $model = $this->customerRepository->create($payload);

        return new CustomerResource($model);
    }

    /**
     * GET: Fetch a single customer
     */
    public function getCustomer(string $uuid)
    {
        $model = $this->customerRepository->findByUuid($uuid);

        return new CustomerResource($model);
    }

    /**
     * PUT: Update customer details
     */
    public function updateCustomer(string $uuid, array $payload)
    {
        $model = $this->customerRepository->update($uuid, $payload);

        return new CustomerResource($model);
    }

    /**
     * DELETE: Remove customer
     */
    public function deleteCustomer(string $uuid)
    {
        $this->customerRepository->delete($uuid);

        return true;
    }

    /**
     * POST: Restore a soft-deleted customer
     */
    public function restoreCustomer(string $uuid)
    {
        $model = $this->customerRepository->restore($uuid);

        return new CustomerResource($model);
    }
}