<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Service\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(OrderStoreRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder($request->validated());

        return response()->json([
            'message' => 'Order created successfully.',
            'data' => $order,
        ], 201);
    }

    public function listByCustomer(Request $request, string $customer): JsonResponse
    {
        $limit = $request->integer('limit');
        $orders = $this->orderService->listOrdersByCustomer($customer, $limit);

        return response()->json([
            'message' => 'Orders retrieved successfully.',
            'data' => $orders,
        ], 200);
    }
}