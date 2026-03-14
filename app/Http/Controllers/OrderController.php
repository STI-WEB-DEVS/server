<?php

namespace App\Http\Controllers;

use App\Service\OrderService;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        return $this->orderService->listOrder(
            $request->input('per_page', 15)
        );
    }

    /**
     * Output #2 — Create Order with items (with validation)
     */
    public function store(OrderRequest $request)
    {
        return $this->orderService->createOrderWithItems(
            $request->validated()
        );
    }

    public function show(string $uuid)
    {
        return $this->orderService->getOrder($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->orderService->updateOrder(
            $uuid,
            $request->all()
        );
    }

    public function destroy(string $uuid)
    {
        $this->orderService->deleteOrder($uuid);
        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully.',
        ], 200);
    }

    public function restore(string $uuid)
    {
        return $this->orderService->restoreOrder($uuid);
    }
}