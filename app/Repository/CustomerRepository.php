<?php

namespace App\Repository;

use App\Models\Customer;

class CustomerRepository
{

    public function create(array $data) {

        return Customer::create($data);
    
      }
    

    public function find(string $id)
    {
        return Customer::findOrFail($id); 
    }

    public function all()
    {
        return Customer::all();
    }

    public function update(string $id, array $data)
    {
        $customer = Customer::findOrFail($id);
        $customer->update($data);
        return $customer;
    }

    public function delete(string $id)
    {
        $customer = $this->find($id);
        return $customer->delete();
    }
}