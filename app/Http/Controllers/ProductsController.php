<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Service\ProductsService;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^(?=.*[A-Za-z])[A-Za-z0-9 ]+$/',
            ],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);
        return $this->productsService->createProducts($request->all());

    }

    public function show(string $uuid)
    {
        return $this->productsService->getProducts($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^(?=.*[A-Za-z])[A-Za-z0-9 ]+$/',
            ],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
        ]);
        return $this->productsService->updateProducts($uuid, $request->all());
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
}