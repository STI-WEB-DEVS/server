<?php

namespace App\Http\Controllers;

// 1. Add this line at the top to import your validation rules
use App\Http\Requests\StoreOrdersRequest; 
use App\Service\OrdersService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrdersController extends Controller
{
    private OrdersService $ordersService;

    public function __construct(OrdersService $ordersService)
    {
        $this->ordersService = $ordersService;
    }

    public function index(Request $request)
    {
        return $this->ordersService->listOrders($request->input('per_page', 15));
    }

    // 2. Change 'Request' to 'StoreOrdersRequest' here
    public function store(StoreOrdersRequest $request) 
    {
        // Using $request->validated() ensures only your designed payload is sent to the service
        return $this->ordersService->createOrders($request->validated());
    }

    // 3. ADD THIS NEW FUNCTION at the bottom to see orders per customer
    public function listByCustomer(string $customer_uuid)
    {
        return $this->ordersService->getOrdersByCustomer($customer_uuid);
    }

    public function show(string $uuid)
    {
        return $this->ordersService->getOrders($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->ordersService->updateOrders($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $this->ordersService->deleteOrders($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    public function restore(string $uuid)
    {
        return $this->ordersService->restoreOrders($uuid);
    }
}