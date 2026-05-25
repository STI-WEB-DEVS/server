<?php

namespace App\Repository;

use App\Models\Orderitem;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderItemsRepository
{
    public function paginate(int $perPage = 15)
    {
        return Orderitem::latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        return Orderitem::create($payload);
    }

    public function findByUuid(string $uuid)
    {
        return Orderitem::where('uuid', $uuid)->firstOrFail();
    }

    public function findByField(string $field, $value)
    {
        return Orderitem::where($field, $value)->firstOrFail();
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
        $model = Orderitem::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();
        return $model;
    }
}