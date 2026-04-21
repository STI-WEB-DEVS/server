<?php

namespace App\Service;

use App\Http\Resources\ProductResource;
use App\Repository\ProductRepository;

class ProductService
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * GET: List products with pagination using ProductResource
     */
    public function listProduct(int $perPage = 15)
    {
        $collection = $this->productRepository->paginate($perPage);

        return ProductResource::collection($collection);
    }

    /**
     * POST: Create product
     */
    public function createProduct(array $payload)
    {
        $model = $this->productRepository->create($payload);

        return new ProductResource($model);
    }

    /**
     * GET: Fetch a single product
     */
    public function getProduct(string $uuid)
    {
        $model = $this->productRepository->findByUuid($uuid);

        return new ProductResource($model);
    }

    /**
     * PUT: Update product details
     */
    public function updateProduct(string $uuid, array $payload)
    {
        $model = $this->productRepository->update($uuid, $payload);

        return new ProductResource($model);
    }

    /**
     * DELETE: Remove product
     */
    public function deleteProduct(string $uuid)
    {
        $this->productRepository->delete($uuid);

        return true;
    }

    /**
     * POST: Restore a soft-deleted product
     */
    public function restoreProduct(string $uuid)
    {
        $model = $this->productRepository->restore($uuid);

        return new ProductResource($model);
    }
}