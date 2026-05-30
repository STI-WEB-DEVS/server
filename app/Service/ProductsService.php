<?php

namespace App\Service;

use App\Repository\ProductsRepository;
use App\Http\Resources\ProductsResource;

class ProductsService
{
    private ProductsRepository $productsRepository;

    public function __construct(ProductsRepository $productsRepository) 
    {
        $this->productsRepository = $productsRepository;
    }

    public function listProducts(int $perPage = 15)
    {
        $collection = $this->productsRepository->paginate($perPage);
        return ProductsResource::collection($collection);
    }

    public function createProducts(array $payload)
    {
        $model = $this->productsRepository->create($payload);
        return new ProductsResource($model);
    }

    public function getProducts(string $uuid)
    {
        $model = $this->productsRepository->findByUuid($uuid);
        return new ProductsResource($model);
    }

    public function getProductsByField(string $field, $value)
    {
        $model = $this->productsRepository->findByField($field, $value);
        return new ProductsResource($model);
    }

    public function updateProducts(string $uuid, array $payload)
    {
        if (isset($payload['add_quantity'])) {
            $model = $this->productsRepository->findByUuid($uuid);
            $payload['quantity'] = $model->quantity + (int)$payload['add_quantity'];
            unset($payload['add_quantity']);
        }
        $model = $this->productsRepository->update($uuid, $payload);
        return new ProductsResource($model);
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
}