<?php

namespace App\Repository;

use App\Models\Customer;

class OrderItemsRepository
{
    public function paginate(int $perPage = 15)
    {
        return OrderItems::latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        return OrderItems::create($payload);
    }

    public function findByUuid(string $uuid)
    {
        return OrderItems::where('uuid', $uuid)->first();
    }

    public function findByField(string $field, $value)
    {
        return OrderItems::where($field, $value)->first();
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
        $model = OrderItems::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();

        return $model;
    }
}
