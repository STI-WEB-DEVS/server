<?php

namespace App\Repository;

use App\Models\Customer;

class CustomerRepository
{
    public function getAll() {
        return Customer::latest()->paginate(5);
    }

    public function findById($uuid) {
       
        return Customer::where('uuid', $uuid)->firstOrFail();
    }

    public function create(array $data) {
        return Customer::create($data);
    }

    public function update($uuid, array $data) {
        
        $customer = Customer::where('uuid', $uuid)->firstOrFail();
        $customer->update($data);
        return $customer;
    }

    public function delete($uuid) {
      
        $customer = Customer::where('uuid', $uuid)->firstOrFail();
        return $customer->delete();
    }
}