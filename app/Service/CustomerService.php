<?php

namespace App\Service;

use App\Http\Resources\CustomerResource;
use App\Repository\CustomerRepository;
use Illuminate\Support\Facades\Hash;

class CustomerService
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function listOfCustomers(int $perPage = 15){
        $collection = $this->customerRepository->paginate($perPage);

        return CustomerResource::collection($collection);
    }

    public function createCustomer(array $payload)
    {
        $model = $this->customerRepository->create($payload);

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

    public function getCustomer(string $uuid)
    {
        $model = $this->customerRepository->findByUuid($uuid);

        return new CustomerResource($model);
    }
}
