<?php

namespace App\Http\Controllers;

use App\Service\OrderItemService;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    private OrderItemService $orderItemService;

    public function __construct(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }

    public function index(Request $request)
    {
        return $this->orderItemService->listOrderItem($request->input('per_page', 15));
    }

    public function store(Request $request)
    {
        return $this->orderItemService->createOrderItem($request->all());
    }

    public function show(string $uuid)
    {
        return $this->orderItemService->getOrderItem($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->orderItemService->updateOrderItem($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $this->orderItemService->deleteOrderItem($uuid);

        return response()->json(['message' => 'Deleted successfully'], 200);
    }

    public function restore(string $uuid)
    {
        return $this->orderItemService->restoreOrderItem($uuid);
    }
}
