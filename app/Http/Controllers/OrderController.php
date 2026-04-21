<?php

namespace App\Http\Controllers;

use App\Service\OrderService;

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

    public function store(Request $request)
    {
        // Basic validation (Ideally, move this to an OrderStoreRequest)
        $validatedData = $request->validate([
            'customer_uuid'          => 'required|uuid|exists:customers,uuid',
            'items'                  => 'required|array|min:1',
            'items.*.product_uuid'   => 'required|uuid|exists:products,uuid',
            'items.*.quantity'       => 'required|integer|min:1',
        ]);

        $orderResource = $this->orderService->createOrder($validatedData);

        return response()->json([
            'message' => 'Order created successfully',
            'data'    => $orderResource
        ], 201);
    }

    public function getByCustomer(string $customerUuid, Request $request)
    {
        $perPage = $request->input('per_page', 15);
        return $this->orderService->listCustomerOrders($customerUuid, $perPage);
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
}