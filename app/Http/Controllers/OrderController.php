<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Service\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\OrderItemsRequest;


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

    public function store(OrderRequest $request): JsonResponse
    {
        $customerUuid = $request->input('customer_uuid');
        $items = $request->input('items');

        $order = $this->orderService->createOrder($customerUuid, $items);

        return response()->json($order,201);
    }

    public function show(string $uuid)
    {
        return $this->orderService->getOrder($uuid);
    }

    public function update(string $uuid, OrderRequest $request): JsonResponse
    {
        $order = $this->orderService->UpdateOrder($uuid, $request->Validated());
        return response()->json($order);
        
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