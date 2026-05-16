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

    public function summary(Request $request)
    {
        return $this->orderService->getSummary($request->only(['from', 'to']));
    }

    public function destroy(string $uuid)
    {
        $this->orderService->deleteOrder($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }

    public function listByCustomer(string $customerUuid)
    {
        return $this->orderService->listOrdersByCustomer($customerUuid, 15);
    }
}
