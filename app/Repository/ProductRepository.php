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
    $product = new Product();
    $product->name = $payload['name'];
    $product->price = $payload['price'];
    $product->save();
    
    return $product;
}

    public function findByUuid(string $uuid)
    {
        return Product::where('uuid', $uuid)->firstOrFail();
    }

    public function findByField(string $field, $value)
    {
        return Product::where($field, $value)->firstOrFail();
    }

    public function store(Request $request)
{
    $product = $this->productService->createProduct($request->all());
    
    return response()->json([
        'message' => 'Product created successfully',
        'data' => $product
    ], 201); 
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