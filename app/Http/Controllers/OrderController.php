<?php
 
namespace App\Http\Controllers;
 
use App\Http\Requests\OrderRequest;
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
 
    public function store(OrderRequest $request): JsonResponse
    {
        //  THE FIX: Grab the logged-in user's UUID from Laravel's Auth guard 
        // instead of relying on the frontend to send it.
        $customerUuid = auth()->user()->uuid; 
 
        $items = $request->input('items');
 
        // This stays completely untouched, but now securely uses the logged-in customer!
        $order = $this->orderService->createOrder($customerUuid, $items);
 
        return response()->json($order, 201);
    }
 
    public function show(string $uuid): JsonResponse
    {
        $order = $this->orderService->getOrder($uuid);
        return response()->json($order);
    }
 
    public function update(string $uuid, OrderRequest $request): JsonResponse
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
 