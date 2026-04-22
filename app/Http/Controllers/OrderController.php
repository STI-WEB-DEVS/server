<?php

namespace App\Http\Controllers;

use App\Service\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(string $customer_uuid)
    {
        return $this->orderService->getOrdersByCustomer($customer_uuid);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_uuid' => 'required|uuid|exists:customers,uuid',
            'product_uuid'  => 'required|uuid|exists:products,uuid',
            'quantity'      => 'required|integer|min:1',
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

        return response()->json(['message' => 'Order deleted successfully'], 200);
    }
}
