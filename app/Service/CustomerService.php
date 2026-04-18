<?php

namespace App\Service;

use App\Repository\CustomerRepository;

class CustomerService
{
    private customerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

   public function createCustomer($payload){
       return $this->customerRepository->createCustomer($payload);
   }

   public function retrieveCustomer($payload){
     return $this->customerRepository->retrieveCustomer($payload);
   }

   public function updateCustomer($payload,$id){
        return $this->customerRepository->updateCustomer($payload,$id);
   }
   public function deleteCustomer($payload){
    return $this->customerRepository->deleteCustomer($payload);
   }

   public function getCustomers(){
       return $this->customerRepository->getAllCustomers();
   }

 
}
