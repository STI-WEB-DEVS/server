<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\UpdateProductRequest;    
use App\Service\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        return $this->productService->listProduct($request->input('per_page', 15));
    }

    public function store(ProductStoreRequest $request)
    {
        return $this->productService->createProduct($request->validated());
    }

    public function show(string $uuid)
    {
        return $this->productService->getProduct($uuid);
    }

    public function update(UpdateProductRequest $request, string $uuid)
    {
        return $this->productService->updateProduct($uuid, $request->validated());
    }

    public function destroy(string $uuid)
    {
        $this->productService->deleteProduct($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}
