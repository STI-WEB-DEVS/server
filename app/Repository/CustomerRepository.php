<?php

namespace App\Repository;

use App\Models\Customer;

class CustomerRepository
{
    public function findByField(string $field, $value)
    {
        return Customer::where($field, $value)->first();
    }
    public function create(array $payload)
    {
        return Customer::create($payload);
    }
    public function findByUuid(string $uuid)
    {
        return Customer::wgere('uid', $uuid)->first();
    }
}
