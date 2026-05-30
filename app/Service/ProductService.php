<?php

namespace App\Service;

use App\Repository\ProductRepository;
use App\Http\Resources\ProductResource;

class ProductService
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function listProduct(int $perPage = 15)
    {
        $collection = $this->productRepository->paginate($perPage);
        return ProductResource::collection($collection);
    }

    public function createProduct(array $payload)
    {
        $model = $this->productRepository->create($payload);
        return new ProductResource($model);
    }

    public function getProduct(string $uuid)
    {
        $model = $this->productRepository->findByUuid($uuid);
        return new ProductResource($model);
    }

    public function getProductByField(string $field, $value)
    {
        $model = $this->productRepository->findByField($field, $value);
        return new ProductResource($model);
    }

    public function updateProduct(string $uuid, array $payload)
    {
        $model = $this->productRepository->update($uuid, $payload);
        return new ProductResource($model);
    }

    public function deleteProduct(string $uuid)
    {
        $this->productRepository->delete($uuid);
        return true;
    }

    public function restoreProduct(string $uuid)
    {
        $model = $this->productRepository->restore($uuid);
        return new ProductResource($model);
    }

    // ← new
    public function adjustStock(string $uuid, int $adjustment)
    {
        $model = $this->productRepository->findByUuid($uuid);

        $newStock = $model->stock_quantity + $adjustment;

        if ($newStock < 0) {
            abort(422, 'Insufficient stock.');
        }

        $model = $this->productRepository->update($uuid, [
            'stock_quantity' => $newStock,
        ]);

        return new ProductResource($model);
    }
}