<?php

namespace App\Http\Controllers;

use App\Service\OrderItemsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderItemsController extends Controller
{
    private OrderItemsService $orderitemsservice;

    public function __construct(OrderItemsService $orderitemsservice)
    {
        $this->orderitemsservice = $orderitemsservice;
    }

    public function index(Request $request)
    {
        return $this->orderitemsservice->listProduct($request->input('per_page', 15));
    }

    public function store(Request $request)
    {
        return $this->orderitemsservice->createProduct($request->all());
    }

    public function show(string $uuid)
    {
        return $this->orderitemsservice->getProduct($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->orderitemsservice->updateProduct($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $this->orderitemsservice->deleteProduct($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    public function restore(string $uuid)
    {
        return $this->orderitemsservice->restoreProduct($uuid);
    }
}