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
        return $this->orderService->listOrder($request->input('per_page', 15));
    }

    public function store(Request $request, OrderService $service)
    {
        $order = $service->store($request->all());
        return response()->json($order, 201);
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

    public function getByCustomer(string $customerUuid)
    {
        return $this->orderService->getOrdersByCustomer($customerUuid);
    }
}