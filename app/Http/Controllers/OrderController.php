<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Service\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(): JsonResponse
    {
        $orders = $this->orderService->listOrder();
        return response()->json($orders);
    }

    public function store(OrderStoreRequest $request): JsonResponse
    {
        $customerId = $request->input('customer_id');
        $items      = $request->input('items');

        $order = $this->orderService->createOrder($customerId, $items);

        return response()->json($order, 201);
    }

    public function show(string $uuid): JsonResponse
    {
        $order = $this->orderService->getOrder($uuid);
        return response()->json($order);
    }

    public function update(string $uuid, OrderStoreRequest $request): JsonResponse
    {
        $order = $this->orderService->updateOrder($uuid, $request->validated());
        return response()->json($order);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->orderService->deleteOrder($uuid);
        return response()->json(null, 204);
    }

    public function restore(string $uuid): JsonResponse
    {
        $order = $this->orderService->restoreOrder($uuid);
        return response()->json($order);
    }
}