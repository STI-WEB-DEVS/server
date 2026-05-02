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
        $customer = $request->user()->customer;

        if (!$customer) {
            return response()->json(['message' => 'No customer profile found.'], 400);
        }

        return $this->orderService->listOrdersForCustomer($customer->id, $request->input('per_page', 15));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_uuid' => 'required|exists:products,uuid',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $customer = $request->user()->customer;

        if (!$customer) {
            return response()->json(['message' => 'User does not have an associated customer profile.'], 400);
        }

        try {
            $order = $this->orderService->placeOrder($customer, $request->items);
            return response()->json($order, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to place order: ' . $e->getMessage()], 500);
        }
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
