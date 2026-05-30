<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Service\ProductService;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        return $this->productService->listProducts($request->input('per_page', 15));
    }

    public function store(ProductStoreRequest $request)
    {
        return $this->productService->createProducts($request->validated());
    }

    public function show(string $uuid)
    {
        return $this->productService->getProducts($uuid);
    }

    public function update(ProductStoreRequest $request, string $uuid)
    {
        return $this->productService->updateProducts($uuid, $request->validated());
    }

    public function destroy(string $uuid)
    {
        $this->productService->deleteProducts($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }

    public function reduceStock(Request $request, string $uuid)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $this->productService->reduceStock($uuid, $request->input('quantity'));

        return response()->json(['message' => 'Stock updated successfully'], 200);
    }
}