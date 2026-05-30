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
        $model = $this->productsRepository->update($uuid, $payload);
        return new ProductsResource($model);
    }

    public function deleteProducts(string $uuid)
    {
        $this->productsRepository->delete($uuid);
        
        // Renumber all product IDs sequentially
        $this->renumberProductIds();
        
        return true;
    }

    /**
     * Renumbers all product IDs sequentially after deletion
     * Also updates all foreign key references in order_items table
     */
    private function renumberProductIds()
    {
        // Disable foreign key checks temporarily
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Get all products ordered by current ID
        $products = \App\Models\Product::orderBy('id')->get();
        
        // Create a mapping of old IDs to new IDs
        $idMapping = [];
        $newId = 1;
        
        foreach ($products as $product) {
            $oldId = $product->id;
            $idMapping[$oldId] = $newId;
            $newId++;
        }
        
        // Update order_items table to use new product IDs
        foreach ($idMapping as $oldId => $newProductId) {
            \DB::table('order_items')
                ->where('product_id', $oldId)
                ->update(['product_id' => $newProductId + 10000]); // Temporary ID to avoid conflicts
        }
        
        // Update product IDs
        foreach ($idMapping as $oldId => $newProductId) {
            \DB::table('products')
                ->where('id', $oldId)
                ->update(['id' => $newProductId]);
        }
        
        // Update order_items back to final product IDs
        foreach ($idMapping as $oldId => $newProductId) {
            \DB::table('order_items')
                ->where('product_id', $newProductId + 10000)
                ->update(['product_id' => $newProductId]);
        }
        
        // Reset auto-increment to next available ID
        \DB::statement('ALTER TABLE products AUTO_INCREMENT = ' . $newId);
        
        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function restoreProducts(string $uuid)
    {
        $model = $this->productsRepository->restore($uuid);
        return new ProductsResource($model);
    }

    /**
     * Restock a product by adding quantity to existing stock
     */
    public function restockProduct(string $uuid, int $quantity)
    {
        $product = $this->productsRepository->findByUuid($uuid);
        $product->quantity += $quantity;
        $product->save();
        
        return new ProductsResource($product);
    }
}