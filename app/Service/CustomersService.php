<?php

namespace App\Service;

use App\Repository\CustomersRepository;
use App\Http\Resources\CustomersResource;

class CustomersService
{
    private CustomersRepository $customersRepository;

    public function __construct(CustomersRepository $customersRepository) 
    {
        $this->customersRepository = $customersRepository;
    }

    public function listCustomers(int $perPage = 15)
    {
        $collection = $this->customersRepository->paginate($perPage);
        return CustomersResource::collection($collection);
    }

    public function createCustomers(array $payload)
    {
        if (!isset($payload['name']) || !isset($payload['email'])) {
        throw new \Exception("Name and Email are required");
    }

    $model = $this->customersRepository->create($payload);

    return new CustomersResource($model);
    }

    public function getCustomers(string $uuid)
    {
        $model = $this->customersRepository->findByUuid($uuid);
        return new CustomersResource($model);
    }

    public function getCustomersByField(string $field, $value)
    {
        $model = $this->customersRepository->findByField($field, $value);
        return new CustomersResource($model);
    }

    public function updateCustomers(string $uuid, array $payload)
    {
        $model = $this->customersRepository->update($uuid, $payload);
        return new CustomersResource($model);
    }

    public function deleteCustomers(string $uuid)
    {
        $this->customersRepository->delete($uuid);
        return true;
    }

    public function restoreCustomers(string $uuid)
    {
        $model = $this->customersRepository->restore($uuid);
        return new CustomersResource($model);
    }
}