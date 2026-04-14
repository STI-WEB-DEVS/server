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
        $result = $this->customerRepository->paginate($perPage);
        CustomerResource::collection($result['data']);

        return [
            'service_message' => 'Success in Service.',
            'resource_message' => 'Success in Resources.',
        ];
    }

    public function createCustomer(array $payload)
    {
        $result = $this->customerRepository->create($payload);
        new CustomerResource($result['data']);

        return [
            'service_message' => 'Success in Service.',
            'resource_message' => 'Success in Resources.',
        ];
    }

    public function getCustomer(string $uuid)
    {
        $result = $this->customerRepository->findByUuid($uuid);
        new CustomerResource($result['data']);

        return [
            'service_message' => 'Success in Service.',
            'repository_message' => $result['repository_message'],
            'resource_message' => 'Success in Resources.',
        ];
    }

    public function updateCustomer(string $uuid, array $payload)
    {
        $result = $this->customerRepository->update($uuid, $payload);
        new CustomerResource($result['data']);

        return [
            'service_message' => 'Success in Service.',
            'repository_message' => $result['repository_message'],
            'resource_message' => 'Success in Resources.',
        ];
    }

    public function deleteCustomer(string $uuid)
    {
        $result = $this->customerRepository->delete($uuid);

        return [
            'service_message' => 'Success in Service.',
            'repository_message' => $result['repository_message'],
            'deleted' => $result['deleted'],
            'resource_message' => 'Success in Resources.',
        ];
    }
}