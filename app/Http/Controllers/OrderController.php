<?php

namespace App\Http\Controllers;

use App\Service\OrderService;
use App\Http\Requests\OrderStoreRequest;
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
     * Making the parameter optional (?string and = null) fixes the "Too few arguments" error.
     */
    public function index(?string $customer_uuid = null) 
    {
        // If a UUID is provided in the URL path, filter by customer
        if ($customer_uuid) {
            return $this->orderService->getOrdersByCustomer($customer_uuid);
        }

        // If no UUID is in the path, show all orders
        return $this->orderService->getAllOrders();
    }

    /**
     * Store a newly created order.
     */
    public function store(OrderStoreRequest $request) 
    {
        return $this->orderService->createOrder($request->validated());
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
        return response()->json(['message' => 'Order deleted successfully'], 200);
    }
}