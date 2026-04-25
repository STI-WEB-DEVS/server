<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Service\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(): JsonResponse
    {
        $orders = $this->orderService->listOrder();
        return response()->json($orders);
    }

    public function customerOrders(string $customerUuid): JsonResponse
    {
        $orders = $this->orderService->getOrdersByCustomerUuid($customerUuid);
        return response()->json($orders);
    }

    public function store(OrderStoreRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder(
            $request->input('customer_uuid'),
            $request->input('items')
        );
        return response()->json($order, 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $order = $this->orderService->getOrder($uuid);
        return response()->json($order);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->orderService->deleteOrder($uuid);
        return response()->json(null, 204);
    }
}