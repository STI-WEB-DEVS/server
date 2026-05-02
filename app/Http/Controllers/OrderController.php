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

    // List all orders (paginated)
    public function index(Request $request)
    {
        return $this->orderService->listOrder($request->input('per_page', 15));
    }

    // Create a new order (always creates, never overwrites)
    public function store(OrderStoreRequest $request)
    {
        return $this->orderService->createOrder($request->validated());
    }

    // Show a single order by UUID
    public function show(string $uuid)
    {
        return $this->orderService->getOrder($uuid);
    }

    // Delete an order by UUID
    public function destroy(string $uuid)
    {
        $this->orderService->deleteOrder($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }

    // List orders by customer (paginated)
    public function listByCustomer(Request $request, string $customerUuid)
    {
        return $this->orderService->listOrdersByCustomer($customerUuid, $request->input('per_page', 15));
    }

    // Get all orders for a customer (non-paginated)
    public function getByCustomer(string $uuid)
    {
        return $this->orderService->getOrdersByCustomer($uuid);
    }
}
