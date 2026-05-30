<?php

namespace App\Http\Controllers;

use App\Service\ProductsService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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

    public function store(StoreProductRequest $request)
    {
        return $this->productsService->createProducts($request->validated());
    }

    public function show(string $uuid)
    {
        return $this->productsService->getProducts($uuid);
    }

    public function update(UpdateProductRequest $request, string $uuid)
    {
        return $this->productsService->updateProducts($uuid, $request->validated());
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
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        return $this->productsService->restockProduct($uuid, $request->input('quantity'));
    }
}
