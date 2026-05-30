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
        // Remove quantity from payload to prevent direct updates
        // Use restockProduct() method instead
        unset($payload['quantity']);
        
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

    public function restockProduct(string $uuid, int $quantity)
    {
        $model = $this->productRepository->findByUuid($uuid);
        $newQuantity = $model->quantity + $quantity;
        $model->update(['quantity' => $newQuantity]);
        return new ProductResource($model);
    }

    public function reduceStock(string $uuid, int $quantity)
    {
        $model = $this->productRepository->findByUuid($uuid);
        
        if ($model->quantity < $quantity) {
            throw new \Exception("Insufficient stock. Available: {$model->quantity}, Requested: {$quantity}");
        }
        
        $newQuantity = $model->quantity - $quantity;
        $model->update(['quantity' => $newQuantity]);
        return new ProductResource($model);
    }
}