<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Service\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(): JsonResponse
    {
        $orders = $this->orderService->listOrder();
        return response()->json($orders);
    }

    public function customerOrders(string $customerUuid)
    {
        $orders = $this->orderService->getOrdersByCustomerUuid($customerUuid);
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        // 1. Get the currently logged-in User model via the token
        $user = $request->user(); 
    
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
    
        // 2. Use the customer relationship on your User model 
        $customer = $user->customer; 
    
        if (!$customer || !$customer->uuid) {
            return response()->json(['message' => 'This account is not linked to a valid customer profile.'], 422);
        }
    
        // 3. Extract the real UUID directly from the customer table row
        $customerTableUuid = $customer->uuid;
    
        try {
            // Pass the customer's true table UUID securely to your service layer
            $order = $this->orderService->createOrder($customerTableUuid, $request->input('items'));
            return $order;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function show(string $uuid): JsonResponse
    {
        $order = $this->orderService->getOrder($uuid);
        return response()->json($order);
    }

    public function update(string $uuid, OrderStoreRequest $request): JsonResponse
    {
        $order = $this->orderService->updateOrder($uuid, $request->validated());
        return response()->json($order);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $this->orderService->deleteOrder($uuid);
        return response()->json(null, 204);
    }
}