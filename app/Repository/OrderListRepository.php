<?php

namespace App\Repository;

use App\Models\OrderList;

class OrderListRepository
{
    public function paginate(int $perPage = 15)
    {
        return OrderList::latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        return OrderList::create($payload);
    }

    public function findByUuid(string $uuid)
    {
        return OrderList::where('uuid', $uuid)->firstOrFail();
    }

    public function findByField(string $field, $value)
    {
        return OrderList::where($field, $value)->firstOrFail();
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
        $model = OrderList::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();

        return $model;
    }
}
