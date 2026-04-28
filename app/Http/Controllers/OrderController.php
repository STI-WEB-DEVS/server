<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Service\OrderService;
use App\Http\Resources\OrderResource;
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

    public function store(OrderStoreRequest $request, OrderService $service)
    {
        $order = $service->store($request->validated());
        return (new OrderResource($order))->response()->setStatusCode(201);
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

    public function getByCustomer(string $customerUuid)
    {
        $orders = $this->orderService->getOrdersByCustomer($customerUuid);
        return response()->json($orders);
    }
}
