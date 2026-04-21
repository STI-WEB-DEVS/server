<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
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

    public function update(Request $request, string $uuid)
    {
        $payload = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0'],
        ]);

        return $this->productService->updateProduct($uuid, $payload);
    }

    public function destroy(string $uuid)
    {
        $this->productService->deleteProduct($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}