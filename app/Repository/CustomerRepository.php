<?php

namespace App\Repository;

use App\Models\Customer;
use App\Models\User;

class CustomerRepository
{
    public function paginate(int $perPage = 15)
    {
        return Customer::latest()->paginate($perPage);
    }

    public function create(array $data)
    {
        return Customer::create($data);
    }

    public function findById($id)
    {
        return Customer::findOrFail($id);
    }

    public function findByField(string $field, $value)
    {
        return Customer::where($field, $value)->firstOrFail();
    }

    public function update(string $uuid, array $payload)
    {
        $model = $this->findByUuid($uuid);
        $model->update($payload);

        return $model;
    }

    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);

        return $model->delete();
    }

    public function restore(string $uuid)
    {
        $model = Customer::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();

        return $model;
    }
}