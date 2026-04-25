<?php

namespace App\Http\Controllers;

use App\Service\OrdersService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrdersController extends Controller
{
    private OrdersService $ordersService;

    public function __construct(OrdersService $ordersService)
    {
        $this->ordersService = $ordersService;
    }

    public function index(Request $request)
    {
        return $this->orderService->listOrders($request->input('per_page', 15));
    
    public function store (Request $request)
    {
        $request->validate([
            'customer_uuid' => 'required|exists:customers,uuid',
            'product_uuid' => 'required|exists:products,uuid',
            'quantity' => 'required|integer|main:1',
        ]);
        return $this->ordersService->createOrders($request->all());
    }
    *
    public function show(string $uuid)
    {
        return $this->ordersService->getOrders($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->ordersService->updateOrders($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $this->ordersService->deleteOrders($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    public function restore(string $uuid)
    {
        return $this->ordersService->restoreOrders($uuid);
    }
}

