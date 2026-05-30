<?php

namespace App\Repository;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductsRepository
{
    public function paginate(int $perPage = 15)
    {
        return Product::latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        return Product::create($payload);
    }

    public function findByUuid(string $uuid)
    {
        return Product::where('uuid', $uuid)->firstOrFail();
    }

    public function findByField(string $field, $value)
    {
        return Product::where($field, $value)->firstOrFail();
    }

    public function update(string $uuid, array $payload)
    {
        $model = $this->findByUuid($uuid);

        $addQuantity = 0;
        if (isset($payload['add_quantity'])) {
            $addQuantity = (int) $payload['add_quantity'];
            unset($payload['add_quantity']);
        }

        $model->update($payload);

        if ($addQuantity > 0) {
            $model->increment('quantity', $addQuantity);
        }

        return $model;
    }

    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);
        return $model->delete();
    }

    public function restore(string $uuid)
    {
        $model = Product::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();
        return $model;
    }
}