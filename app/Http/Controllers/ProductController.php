<?php

namespace App\Http\Controllers;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        return $this->productService->createProduct($validated);
    }

    public function show(string $uuid)
    {
        return $this->productService->getProduct($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        return $this->productService->updateProduct($uuid, $validated);
    }

    public function destroy(string $uuid)
    {
        $this->productService->deleteProduct($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    public function restore(string $uuid)
    {
        return $this->productService->restoreProduct($uuid);
    }
}
