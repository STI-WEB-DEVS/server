<?php

namespace App\Service;
use App\Repository\UserRepository;
use App\Repository\CustomerRepository;


// use App\Http\Resources\LanguageResource;
class CustomerService
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getCustomers()
    {
        return "list of customers";
    }
    public function listLanguage(int $perPage = 15)
    {
        $collection = $this->customerRepository->paginate($perPage);

        return CustomerRepository::collection($collection);
    }

    public function createLanguage(array $payload)
    {
        $model = $this->customerRepository->create($payload);

        return new CustomerRepository($model);
    }

    public function getLanguage(string $uuid)
    {
        $model = $this->customerRepository->findByUuid($uuid);

        return new CustomerRepository($model);
    }

    public function getLanguageByField(string $field, $value)
    {
        $model = $this->customerRepository->findByField($field, $value);

        return new CustomerRepository($model);
    }

    public function updateLanguage(string $uuid, array $payload)
    {
        $model = $this->customerRepository->update($uuid, $payload);

        return new CustomerRepository($model);
    }

    public function deleteLanguage(string $uuid)
    {
        $this->customerRepository->delete($uuid);

        return true;
    }
}
