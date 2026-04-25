<?php

namespace App\Repository;

use App\Models\Customer;

class CustomerRepository
{

    public function paginate(int $perPage = 15)
    {
        return Customer::latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        // This will return the existing record if the email matches, 
    // or create a new one if it doesn't.
    return Customer::firstOrCreate(
        ['email' => $data['email']], // Search criteria
        [
            'name' => $data['name'],
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            // add other fields here...
        ]
    );
    }

    public function findByUuid(string $uuid)
    {
        return Customer::where('uuid', $uuid)->first();
    }


    public function update(string $uuid, array $payload)
    {
        $model = $this->findByUuid($uuid);
        $model->update($payload);

        return $model;
    }

    public function findByField(string $field, $value)
    {
        return Customer::where($field, $value)->firstOrFail();
    }

    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);

        return $model->delete();
    }

}
