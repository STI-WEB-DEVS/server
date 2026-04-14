<?php

namespace App\Repository;

use App\Models\Customer;

class CustomerRepository
{
    /**
     * Get all customers with pagination.
     */
    public function getAll(int $perPage = 15)
    {
        return Customer::latest()->paginate($perPage);
    }

    /**
     * Create a new customer.
     */
    public function create(array $payload)
    {
        return Customer::create($payload);
    }

    /**
     * Find a single customer by UUID.
     */
    public function findByUuid(string $uuid)
    {
        return Customer::where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Find a customer by a specific field.
     */
    public function findByField(string $field, $value)
    {
        return Customer::where($field, $value)->firstOrFail();
    }

    /**
     * Update an existing customer.
     */
    public function update(string $uuid, array $payload)
    {
        $model = $this->findByUuid($uuid);
        $model->update($payload);

        return $model;
    }

    /**
     * Delete a customer.
     */
    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);

        return $model->delete();
    }

    /**
     * Restore a soft-deleted customer.
     */
    public function restore(string $uuid)
    {
        $model = Customer::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();

        return $model;
    }
}