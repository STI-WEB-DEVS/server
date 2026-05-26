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

    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $products = Product::all();
        
        return response()->json($products);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        // Add basic validation for price and stock consistency
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        return $this->productService->createProduct($validated);
    }

    /**
     * Display the specified product.
     */
    public function show(string $uuid)
    {
        return $this->productService->getProduct($uuid);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, string $uuid)
    {
        return $this->productService->updateProduct($uuid, $request->all());
    }

    /**
     * Remove the specified product.
     */
    public function destroy(string $uuid)
    {
        $this->productService->deleteProduct($uuid);

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

    /**
     * Restore a soft-deleted product.
     */
    public function restore(string $uuid)
    {
        return $this->productService->restoreProduct($uuid);
    }
}