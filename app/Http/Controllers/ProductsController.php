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
        return $this->productsService->createProducts($request->all());
    }

    public function show(string $uuid)
    {
        return $this->productsService->getProducts($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->productsService->updateProducts($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $this->productsService->deleteProducts($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}
