<?php

namespace App\Service;

use App\Repository\CustomerRepository;

class CustomerService
{
    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getAllCustomers() {
        return $this->customerRepository->getAll();
    }

    public function getCustomer($id) {
        return $this->customerRepository->findById($id);
    }

    public function createCustomer(array $data) {
        return $this->customerRepository->create($data);
    }

    public function updateCustomer($id, array $data) {
        return $this->customerRepository->update($id, $data);
    }

    public function deleteCustomer($id) {
        return $this->customerRepository->delete($id);
    }
}