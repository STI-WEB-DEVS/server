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
        return $this->customerRepository->paginate($perPage);
    }

    public function createCustomer(array $payload)
    {
        return $this->customerRepository->create($payload);
    }

    public function getCustomer(string $uuid)
    {
        return $this->customerRepository->findByUuid($uuid);
    }

    public function updateCustomer(string $uuid, array $payload)
    {
        return $this->customerRepository->update($uuid, $payload);
    }

    public function deleteCustomer(string $uuid)
    {
        return $this->customerRepository->delete($uuid);
    }
}