<?php

namespace App\Service;

use App\Repository\ProductRepository;

class ProductService
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository) 
    {
        $this->productRepository = $productRepository;
    }

    public function listProduct(int $perPage = 15)
    {
        return $this->productRepository->paginate($perPage);
    }

    public function createProduct(array $payload)
    {
        return $this->productRepository->create($payload);
    }

    public function getProduct(string $uuid)
    {
        return $this->productRepository->findByUuid($uuid);
    }

    public function updateProduct(string $uuid, array $payload)
    {
        return $this->productRepository->update($uuid, $payload);
    }

    public function deleteProduct(string $uuid)
    {
        $this->productRepository->delete($uuid);
        return true;
    }

    public function restoreProduct(string $uuid)
    {
        return $this->productRepository->restore($uuid);
    }
}