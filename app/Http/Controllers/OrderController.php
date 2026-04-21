<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Service\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        return $this->orderService->listOrder($request->input('per_page', 15));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(OrderStoreRequest $request)
    {
        return $this->orderService->createOrder($request->all());
    }

    /**
     * Display the specified order.
     */
    public function show(string $uuid)
    {
        return $this->orderService->getOrder($uuid);
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, string $uuid)
    {
        // Note: You might want to create an OrderUpdateRequest for specific validation
        return $this->orderService->updateOrder($uuid, $request->all());
    }

    /**
     * Remove the specified order from storage.
     */
    public function destroy(string $uuid)
    {
        $this->orderService->deleteOrder($uuid);

        return response()->json(['message' => 'Order deleted successfully'], 200);
    }

    /**
     * Restore a soft-deleted order.
     */
    public function restore(string $uuid)
    {
        return $this->orderService->restoreOrder($uuid);
    }
}