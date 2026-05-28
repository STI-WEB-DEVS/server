<?php

namespace App\Http\Controllers;

use App\Service\OrderService;
use Illuminate\Http\Request;

class OrdersController extends Controller
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_uuid'        => ['required', 'uuid'],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.product_uuid' => ['required', 'uuid'],
            'items.*.quantity'     => ['required', 'integer', 'min:1'],
        ]);

        return $this->orderService->createOrder($validated);
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

    public function myOrders(Request $request)
    {
        return $this->orderService->listCustomerOrders($request->user());
    }
}
