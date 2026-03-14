<?php

namespace App\Http\Controllers;

use App\Service\ProductService;
use App\Http\Requests\ProductRequest;
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
        return $this->productService->listProduct(
            $request->input('per_page', 15)
        );
    }

    /**
     * Output #1 — Create Product (with validation)
     */
    public function store(ProductRequest $request)
    {
        return $this->productService->createProduct(
            $request->validated()
        );
    }

    public function show(string $uuid)
    {
        return $this->productService->getProduct($uuid);
    }

    public function update(ProductRequest $request, string $uuid)
    {
        return $this->productService->updateProduct(
            $uuid,
            $request->validated()
        );
    }

    public function destroy(string $uuid)
    {
        $this->productService->deleteProduct($uuid);
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ], 200);
    }

    public function restore(string $uuid)
    {
        return $this->productService->restoreProduct($uuid);
    }
}