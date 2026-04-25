<?php

namespace App\Http\Controllers;

use App\Service\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        return $this->orderService->listProduct($request->input('per_page', 15));
    }

    public function store(Request $request)
    {
        return $this->orderService->createProduct($request->all());
    }

    public function show(string $uuid)
    {
        return $this->orderService->getProduct($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->orderService->updateProduct($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $this->orderService->deleteProduct($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    public function restore(string $uuid)
    {
        return $this->orderService->restoreProduct($uuid);
    }
}