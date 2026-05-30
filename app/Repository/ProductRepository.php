<?php

namespace App\Repository;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductRepository
{
    public function paginate(int $perPage = 15)
    {
        return Product::latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        if (isset($payload['restock_amount'])) {
            $restock = intval($payload['restock_amount']);
            if ($restock > 0) {
                $payload['quantity'] = ($payload['quantity'] ?? 0) + $restock;
            }
        }
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
        if (isset($payload['restock_amount'])) {
            $restock = intval($payload['restock_amount']);
            if ($restock > 0) {
                $payload['quantity'] = ($model->quantity ?? 0) + $restock;
            }
        }
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
        $model = Product::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();
        return $model;
    }
}