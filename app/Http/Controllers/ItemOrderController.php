<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ItemOrderService;
use Illuminate\Http\JsonResponse;

class ItemOrderController extends Controller
{
    private ItemOrderService $orderService;

    /**
     * Inject the Service layer via the constructor.
     */
    public function __construct(ItemOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of all orders.
     */
    public function index(): JsonResponse
    {
        $orders = $this->orderService->listOrders();
        return response()->json($orders);
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validate the incoming request
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items'       => 'required|array',
            'items.*.id'  => 'required|exists:items,id',
            'items.*.qty' => 'required|integer|min:1',
            'total_price' => 'required|numeric'
        ]);

        // 2. Call the service to handle business logic (saving to DB)
        $order = $this->orderService->createOrder($validatedData);

        return response()->json([
            'message' => 'Order created successfully',
            'data'    => $order
        ], 201);
    }

    /**
     * Display the specified order.
     */
    public function show(string $uuid): JsonResponse
    {
        $order = $this->orderService->getOrder($uuid);
        
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    /**
     * Update an order (e.g., updating status).
     */
    public function update(Request $request, string $uuid): JsonResponse
    {
        $validatedData = $request->validate([
            'status' => 'required|string|in:pending,completed,cancelled'
        ]);

        $updatedOrder = $this->orderService->updateOrder($uuid, $validatedData);

        return response()->json([
            'message' => 'Order updated successfully',
            'data'    => $updatedOrder
        ]);
    }

    /**
     * Remove the specified order.
     */
    public function destroy(string $uuid): JsonResponse
    {
        $this->orderService->deleteOrder($uuid);
        return response()->json(['message' => 'Order deleted successfully'], 200);
    }
}