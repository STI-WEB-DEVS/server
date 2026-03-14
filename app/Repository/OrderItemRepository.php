<?php

namespace App\Repository;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderItemRepository
{
    public function paginate(int $perPage = 15)
    {
        return OrderItem::latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        return OrderItem::create($payload);
    }

    public function findById(int $id)
    {
        return OrderItem::findOrFail($id);
    }

    public function findByField(string $field, $value)
    {
        return OrderItem::where($field, $value)->firstOrFail();
    }

    public function update(int $id, array $payload)
    {
        $model = $this->findById($id);
        $model->update($payload);
        return $model;
    }

    public function delete(int $id)
    {
        $model = $this->findById($id);
        return $model->delete(); // hard delete
    }

    // Removed restore() method completely
}
