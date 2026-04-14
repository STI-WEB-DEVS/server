<?php

namespace App\Repository;

use App\Models\User;

class CustomerRepository
{
    public function create(array $data) {

        return Customer::create($data);
    }
    public function findById(int $id) {

        return Customer::findOrFail($id);
    
      }
}
