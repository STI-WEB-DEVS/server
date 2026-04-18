<?php

namespace App\Service;

use App\Http\Resources\CompanyResource;
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

        return CompanyResource::collection($collection);
    }

    public function createCustomer(array $payload)
    {
        $model = $this->customerRepository->create($payload);

        return new CompanyResource($model);
    }

    public function getCompany(string $uuid)
    {
        $model = $this->customerRepository->findByUuid($uuid);

        return new CompanyResource($model);
    }

    public function getCompanyByField(string $field, $value)
    {
        $model = $this->customerRepository->findByField($field, $value);

        return new CompanyResource($model);
    }

    public function customerUpdate(array $payload,string $uuid)
    {
        $model = $this->customerRepository->update($uuid, $payload);

        return new CompanyResource($model);
    }

    public function deleteCustomer(string $uuid)
    {
        $this->customerRepository->delete($uuid);

        return true;
    }

    public function restoreCompany(string $uuid)
    {
        $model = $this->customerRepository->restore($uuid);

        return new CompanyResource($model);
    }
}
