<?php
 
namespace App\Service;
 
use App\Repository\CustomerRepository;
use App\Models\Customer;
 
class CustomerService
{
    protected $customerRepository;
 
    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;
    }
 
    public function getAllCustomers()
    {
        return $this->repository->all();
    }
 
    public function getCustomerById($id): ?Customer
    {
        return $this->customerRepository->find($id);
    }
}