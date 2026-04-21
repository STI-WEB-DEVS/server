<?php

namespace App\Http\Controllers;

use App\Service\ItemOrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ItemOrderController extends Controller
{
    private ItemOrderService $itemOrderService;

    public function __construct(ItemOrderService $itemOrderService)
    {
        $this->itemOrderService = $itemOrderService;
    }

    public function index(Request $request)
    {
        return $this->itemOrderService->listItemOrder($request->input('per_page', 15));
    }

    public function store(Request $request)
    {
        return $this->itemOrderService->createItemOrder($request->all());
    }

    public function show(string $uuid)
    {
        return $this->itemOrderService->getItemOrder($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->itemOrderService->updateItemOrder($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $this->itemOrderService->deleteItemOrder($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    public function restore(string $uuid)
    {
        return $this->itemOrderService->restoreItemOrder($uuid);
    }
}