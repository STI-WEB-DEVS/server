<?php

namespace App\Http\Controllers;

use App\Service\OrderItemsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderItemsController extends Controller
{
    private OrderItemsService $orderItemsService;

    public function __construct(OrderItemsService $orderItemsService)
    {
        $this->orderItemsService = $orderItemsService;
    }

    public function index(Request $request)
    {
        return $this->orderItemsService->listOrderItems($request->input('per_page', 15));
    }

    public function store(Request $request)
    {
        return $this->orderItemsService->createOrderItems($request->all());
    }

    public function show(string $uuid)
    {
        return $this->orderItemsService->getOrderItems($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->orderItemsService->updateOrderItems($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $this->orderItemsService->deleteOrderItems($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    public function restore(string $uuid)
    {
        return $this->orderItemsService->restoreOrderItems($uuid);
    }
}