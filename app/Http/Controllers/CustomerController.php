<?php

namespace App\Http\Controllers;

use App\Service\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        return $this->customerService->listCustomer($request->input('per_page', 15));
    }

    public function store(Request $request)
    {
        return $this->customerService->createCustomer($request->all());
    }

    public function show(string $uuid)
    {
        return $this->customerService->getCustomer($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        return $this->customerService->updateCustomer($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $this->customerService->deleteCustomer($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    public function restore(string $uuid)
    {
        return $this->customerService->restoreCustomer($uuid);
    }
}