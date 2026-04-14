<?php

namespace App\Service;

use App\Http\Resources\CustomerResource;
use App\Repository\CustomerRepository;

class CustomerService {

    public function create(array $data) {
  
      // validate, transform, then:
  
      return $this->repo->create($data);
  
    }
  
  }