<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomerRepository

{
    public function getAll()
    {
        return Customer::all();
    }

    public function findById($id)
    {
        return Customer::findOrFail($id);
    }

    public function create(array $data)
    {
        return Customer::create($data);
    }

    public function update($id, array $data)
    {
        $customer = $this->findById($id);
        $customer->update($data);
        return $customer;
    }

    public function delete($id)
    {
        $customer = $this->findById($id);
        return $customer->delete();
    }
}