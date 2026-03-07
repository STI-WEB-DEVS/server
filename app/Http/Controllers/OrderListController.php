<?php

namespace App\Http\Controllers;

use App\Service\OrderListService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderListController extends Controller
{
    private OrderListService $orderListService;

    public function __construct(OrderListService $orderListService)
    {
        $this->orderListService = $orderListService;
    }

    public function index(Request $request)
    {
        return $this->orderListService->listOrderList($request->input('per_page', 15));
    }

    public function store(Request $request)
    {
        return $this->orderListService->createOrderList($request->all());
    }

    public function show(string $uuid)
    {
        return $this->orderListService->getOrderList($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->orderListService->updateOrderList($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $this->orderListService->deleteOrderList($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    public function restore(string $uuid)
    {
        return $this->orderListService->restoreOrderList($uuid);
    }
}