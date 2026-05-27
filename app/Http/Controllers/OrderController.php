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
        try {
            // Use the authenticated user's ID, which is safer than relying on a potentially null UUID
            $user = auth()->user();
            
            // Ensure the user actually has an associated customer_id
            if (!$user->customer_id) {
                throw new \Exception("Authenticated user has no customer profile linked.");
            }

            $items = $request->input('items');

            // Proceed to service
            $order = $this->orderService->createOrder($user->uuid ?? '', $items);

            // Instead of resolving the resource directly, return a simplified success structure
            return response()->json([
                'success' => true,
                'message' => 'Order created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Checkout error: ' . $e->getMessage()
            ], 422);
        }
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
 