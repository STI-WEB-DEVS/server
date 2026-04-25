<?php

namespace App\Http\Controllers;

use App\Service\OrderService;
use App\Http\Requests\StoreOrderRequest; 
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
        return $this->orderService->listOrder($request->input('per_page', 15));
    }

    // UPDATED: Gumagamit na ng StoreOrderRequest para ma-validate bago pumasok sa Service
    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderService->createOrder($request->validated());

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order
        ], 201);
    }

    // NEW: Output #3 - Order list per customer
    public function customerOrders(string $customer_uuid)
    {
        $orders = $this->orderService->getCustomerOrders($customer_uuid);

        return response()->json([
            'message' => 'Customer orders retrieved successfully',
            'data' => $orders
        ], 200);
    }

    public function show(string $uuid)
    {
        return $this->orderService->getOrder($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->orderService->updateOrder($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $this->orderService->deleteOrder($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    public function restore(string $uuid)
    {
        return $this->orderService->restoreOrder($uuid);
    }
}