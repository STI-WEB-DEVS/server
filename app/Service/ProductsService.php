<?php

namespace App\Service;

use App\Repository\ProductsRepository;
use App\Http\Resources\ProductsResource;
use App\Models\OrderItem;

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
        // Required fields
        if (empty($payload['name']) || !isset($payload['price'])) {
            throw new \Exception("Name and Price are required.");
        }

        // Name must not be purely numeric (digits only)
        if (preg_match('/^\d+$/', trim($payload['name']))) {
            throw new \Exception("Product name cannot be numbers only.");
        }

        // Price must be greater than 0
        if ((float) $payload['price'] <= 0) {
            throw new \Exception("Price must be greater than 0.");
        }

        // stock_quantity required on create
        if (!isset($payload['stock_quantity']) || (int) $payload['stock_quantity'] < 0) {
            throw new \Exception("Stock quantity must be 0 or more.");
        }

        $model = $this->productsRepository->create([
            'name'           => $payload['name'],
            'description'    => $payload['description'] ?? null,
            'price'          => $payload['price'],
            'stock_quantity' => (int) $payload['stock_quantity'],
        ]);

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
        // Name validation if being updated
        if (isset($payload['name'])) {
            if (empty(trim($payload['name']))) {
                throw new \Exception("Product name cannot be empty.");
            }
            if (preg_match('/^\d+$/', trim($payload['name']))) {
                throw new \Exception("Product name cannot be numbers only.");
            }
        }

        // Price validation if being updated
        if (isset($payload['price']) && (float) $payload['price'] <= 0) {
            throw new \Exception("Price must be greater than 0.");
        }

        // Only allow safe fields on update (no stock_quantity — use restock instead)
        $allowed = array_filter([
            'name'        => $payload['name'] ?? null,
            'description' => $payload['description'] ?? null,
            'price'       => $payload['price'] ?? null,
        ], fn($v) => !is_null($v));

        $model = $this->productsRepository->update($uuid, $allowed);
        return new ProductsResource($model);
    }

    public function restockProduct(string $uuid, int $quantity)
    {
        if ($quantity <= 0) {
            throw new \Exception("Restock quantity must be greater than 0.");
        }

        $model = $this->productsRepository->restock($uuid, $quantity);
        return new ProductsResource($model);
    }

    public function deleteProducts(string $uuid)
    {
        $product = $this->productsRepository->findByUuid($uuid);
        OrderItem::where('product_id', $product->id)->delete();
        return $product->delete();
    }

    public function restoreProducts(string $uuid)
    {
        $model = $this->productsRepository->restore($uuid);
        return new ProductsResource($model);
    }
}