<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\OrderService;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_uuid' => 'required|exists:customers,uuid',
            'product_uuid'  => 'required|exists:products,uuid',
            'quantity'      => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $order = $this->orderService->createOrder($request->all());

        return response()->json([
            'message' => 'Order created successfully!',
            'data' => $order
        ], 201);
    }

    public function index($customer_uuid)
    {
        $orders = $this->orderService->getOrdersByCustomer($customer_uuid);
        return response()->json([
            'success' => true,
            'data' => $orders
        ], 200);
    }

    public function show(string $customer_uuid) {
        return $this->orderService->getOrdersByCustomer($customer_uuid);
    }
}