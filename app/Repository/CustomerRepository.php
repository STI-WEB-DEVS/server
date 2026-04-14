<?php

namespace App\Repository;

use App\Models\Customer;

class CustomerRepository {

  public function create(array $data) {

    return Customer::create($data);

  }

  public function findById(int $id) {

    return Customer::findOrFail($id);

  }

  public function all() {

    return Customer::all();

  }

}