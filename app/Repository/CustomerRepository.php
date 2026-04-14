<?php

namespace App\Repository;

use App\Models\Customer;

class CustomerRepository
{
    /**
     * Get paginated customers, ordered by newest first.
     */
    public function paginate(int $perPage = 15)
    {
        return Customer::latest()->paginate($perPage);
    }

    /**
     * Create a new customer record.
     */
    public function create(array $payload)
    {
        return Customer::create($payload);
    }

    /**
     * Find a customer by UUID or throw 404.
     */
    public function findByUuid(string $uuid)
    {
        return Customer::where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Find a customer by a specific field and value.
     */
    public function findByField(string $field, $value)
    {
        return Customer::where($field, $value)->firstOrFail();
    }

    /**
     * Update an existing customer record.
     */
    public function update(string $uuid, array $payload)
    {
        $model = $this->findByUuid($uuid);
        $model->update($payload);

        return $model;
    }

    /**
     * Delete a customer record.
     */
    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);

        return $model->delete();
    }

    /**
     * Restore a soft-deleted customer record.
     */
    public function restore(string $uuid)
    {
        $model = Customer::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();

        return $model;
    }
}