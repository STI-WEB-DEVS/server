<?php

namespace App\Http\Controllers;

use App\Service\ProductsService;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    private ProductsService $productsService;

    public function __construct(ProductsService $productsService)
    {
        $this->productsService = $productsService;
    }

    public function index(Request $request)
    {
        return $this->productsService->listProducts($request->input('per_page', 15));
    }

    public function store(Request $request)
    {
        try {
            return $this->productsService->createProducts($request->all());
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show(string $uuid)
    {
        return $this->productsService->getProducts($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        try {
            return $this->productsService->updateProducts($uuid, $request->all());
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(string $uuid)
    {
        $this->productsService->deleteProducts($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }

    public function restore(string $uuid)
    {
        return $this->productsService->restoreProducts($uuid);
    }

    public function restock(Request $request, string $uuid)
    {
        try {
            $quantity = (int) $request->input('quantity', 0);
            return $this->productsService->restockProduct($uuid, $quantity);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}