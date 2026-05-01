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

 // app/Http/Controllers/OrderController.php

public function indexByCustomer($customerUuid)
{
    // Calling the method we just added to your OrderService
    return $this->orderService->getOrdersByCustomer($customerUuid);
}
    
    public function store(Request $request) 
    { 
    $validated = $request->validate([
    'customer_uuid' => 'required|uuid',
    'items' => 'required|array|min:1', 
    'items.*.product_uuid' => 'required|uuid',
    'items.*.quantity' => 
    'required|integer|min:1', ]); 
    return $this->orderService->createOrder($validated); 
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