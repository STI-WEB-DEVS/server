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

    public function indexByCustomer($customerUuid)
    {
        return $this->orderService->getOrdersByCustomer($customerUuid);
    }

    public function index(Request $request)
    {
        return $this->orderService->listOrder($request->input('per_page', 15));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_uuid' => 'required|uuid|exists:customers,uuid',
            'items' => 'required|array|min:1',
            'items.*.product_uuid' => 'required|uuid|exists:products,uuid',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return $this->orderService->createOrder($validated);
    }

    public function show(string $uuid)
    {
        return $this->orderService->getOrder($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        $validated = $request->validate([
            'customer_uuid' => 'sometimes|required|uuid|exists:customers,uuid',
            'items' => 'sometimes|required|array|min:1',
            'items.*.product_uuid' => 'required_with:items|uuid|exists:products,uuid',
            'items.*.quantity' => 'required_with:items|integer|min:1',
        ]);

        return $this->orderService->updateOrder($uuid, $validated);
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
