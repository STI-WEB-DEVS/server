<?php

namespace App\Repository;

use App\Models\Customer;

class CustomerRepository
{
    public function paginate(int $perPage = 15)
    {
        return [
            'repository_message' => 'Success in Repository.',
            'data' => Customer::latest()->paginate($perPage),
        ];
    }

    public function create(array $payload)
    {
        return [
            'repository_message' => 'Success in Repository.',
            'data' => Customer::create($payload),
        ];
    }

    public function findByUuid(string $uuid)
    {
        return [
            'repository_message' => 'Found Successfully',
            'data' => Customer::where('uuid', $uuid)->firstOrFail(),
        ];
    }

    public function update(string $uuid, array $payload)
    {
        $model = Customer::where('uuid', $uuid)->firstOrFail();
        $model->update($payload);

        return [
            'repository_message' => 'Updated Succesfully',
            'data' => $model,
        ];
    }

    public function delete(string $uuid)
    {
        $model = Customer::where('uuid', $uuid)->first();

        if (! $model) {
            return [
                'repository_message' => 'No UUID record matches found.',
                'deleted' => false,
                'data' => false,
            ];
        }

        return [
            'repository_message' => 'Deleted Successfully',
            'deleted' => true,
            'data' => $model->delete(),
        ];
    }
}