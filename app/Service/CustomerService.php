<?php

namespace App\Service;

use App\Repository\CustomerRepository;

class CustomerService {

    private CustomerRepository $repo;

    public function __construct(CustomerRepository $repo) {
        $this->repo = $repo;
    }

    public function create(array $data) {

    return $this->repo->create($data);

  }

  public function listCustomers() {

    return $this->repo->all();

  }

}