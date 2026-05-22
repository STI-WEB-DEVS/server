<?php

namespace App\Http\Controllers;

use App\Service\CustomersService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomersController extends Controller
{
    private CustomersService $customersService;

    public function __construct(CustomersService $customersService)
    {
        $this->customersService = $customersService;
    }

    public function index(Request $request)
    {
        return $this->customersService->listCustomers($request->input('per_page', 15));
    }

    public function store(Request $request)
    {
        return $this->customersService->createCustomers($request->all());
    }

    public function show(string $uuid)
    {
        return $this->customersService->getCustomers($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->customersService->updateCustomers($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $this->customersService->deleteCustomers($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    public function restore(string $uuid)
    {
        return $this->customersService->restoreCustomers($uuid);
    }

    public function orders(string $uuid)
{
    // Ask the service layer to fetch orders for this customer
    return $this->customersService->getCustomerOrders($uuid);
}




}