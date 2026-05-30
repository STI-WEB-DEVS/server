<?php

namespace App\Service;

use App\Repository\ProductsRepository;
use App\Http\Resources\ProductResource;
use Illuminate\Validation\ValidationException;

class ProductService
{
    private ProductsRepository $productsRepository;

    public function __construct(ProductsRepository $productsRepository) 
    {
        $this->productsRepository = $productsRepository;
    }

    public function listProducts(int $perPage = 15)
    {
        $collection = $this->productsRepository->paginate($perPage);
        return ProductResource::collection($collection);
    }

    public function createProducts(array $payload)
    {
        $model = $this->productsRepository->create($payload);
        return new ProductResource($model);
    }

    public function getProducts(string $uuid)
    {
        $model = $this->productsRepository->findByUuid($uuid);
        return new ProductResource($model);
    }

    public function getProductsByField(string $field, $value)
    {
        $model = $this->productsRepository->findByField($field, $value);
        return new ProductResource($model);
    }

    public function updateProducts(string $uuid, array $payload)
    {
        $model = $this->productsRepository->update($uuid, $payload);
        return new ProductResource($model);
    }

    public function deleteProducts(string $uuid)
    {
        $this->productsRepository->delete($uuid);
        return true;
    }

    public function restoreProducts(string $uuid)
    {
        $model = $this->productsRepository->restore($uuid);
        return new ProductsResource($model);
    }

    public function reduceStock(string $uuid, int $quantity): void
    {
        $model = $this->productsRepository->findByUuid($uuid);

        if ($model->stocks < $quantity) {
            throw ValidationException::withMessages([
                'stocks' => "Insufficient stock. Only {$model->stocks} left.",
            ]);
        }

        $model->decrement('stocks', $quantity);
    }
}