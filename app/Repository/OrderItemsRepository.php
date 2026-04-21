<?php

namespace App\Repository;

use App\Models\OrderItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderItemsRepository
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return OrderItem::latest()->paginate($perPage);
    }

    public function create(array $payload): OrderItem
    {
        return OrderItem::create($payload);
    }

    public function findByUuid(string $uuid): OrderItem
    {
        return OrderItem::where('uuid', $uuid)->firstOrFail();
    }

    public function findByField(string $field, $value): OrderItem
    {
        return OrderItem::where($field, $value)->firstOrFail();
    }

    public function findAllByField(string $field, $value)
    {
        return OrderItem::where($field, $value)->get();
    }

    public function update(string $uuid, array $payload): OrderItem
    {
        $model = $this->findByUuid($uuid);
        $model->update($payload);

        return $model;
    }

    public function delete(string $uuid): bool
    {
        $model = $this->findByUuid($uuid);
        return (bool) $model->delete();
    }

    public function restore(string $uuid): OrderItem
    {
        $model = OrderItem::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();

        return $model;
    }
}
