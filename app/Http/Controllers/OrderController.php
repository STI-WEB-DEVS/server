<?php

namespace App\Http\Controllers;

use App\Service\OrderService;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Requests\CustomerOrderListRequest;
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

    public function store(OrderStoreRequest $request)
    {
        return $this->orderService->createOrder($request->validated());
    }

    public function show(string $uuid)
    {
        return $this->orderService->getOrder($uuid);
    }


    public function update(OrderUpdateRequest $request, string $uuid)
    {
        return $this->orderService->updateOrder($uuid, $request->validated());
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