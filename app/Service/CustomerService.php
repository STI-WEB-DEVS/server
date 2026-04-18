<?php

namespace App\Service;

use App\Repository\CustomerRepository;

class CustomerService
{
    public function create(array $data) {

        // validate, transform, then:
    
        return $this->repo->create($data);
    }

    public function __construct(
        protected CustomerRepository $repository
    ) {}

    public function getAllCustomers()
    {
        return $this->repository->all();
    }

    public function createCustomer(array $data)
    {
        return $this->repository->create($data);
    }

    public function getCustomerById(string $id)
    {
        return $this->repository->find($id);
    }

    public function updateCustomer(string $id, array $data)
    {
        return $this->customerRepository->update($id, $data);
    }

    public function deleteCustomer(string $id)
    {
        return $this->repository->delete($id);
    }
}