<?php

namespace App\Repository;

use App\Models\Product;

class ProductRepository
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    /**
     * Create a new product
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Find a product by its UUID
     */
    public function findByUuid(string $uuid)
    {
        return $this->model->where('uuid', $uuid)->firstOrFail();
    }

    /**
     * Get paginated products for the index
     */
    public function paginate(int $perPage)
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Update a product by UUID
     */
    public function update(string $uuid, array $data)
    {
        $product = $this->findByUuid($uuid);
        $product->update($data);
        return $product;
    }

    /**
     * Delete a product by UUID
     */
    public function delete(string $uuid)
    {
        return $this->findByUuid($uuid)->delete();
    }
}