<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\CustomerService;

class CustomerController extends Controller
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    // Fixed: Added Request $request as a parameter
    public function index(Request $request)
    {
        return $this->customerService->listCustomer($request->input('per_page', 15));
    }

    public function store(Request $request)
    {
        return $this->customerService->createCustomer($request->all());
    }

    // Fixed: Changed $uuid to $id to match the method signature
    public function show(string $id)
    {
        return $this->customerService->getCustomer($id);
    }

    // Fixed: Changed $uuid to $id
    public function update(Request $request, string $id)
    {
        return $this->customerService->updateCustomer($id, $request->all());
    }

    // Fixed: Changed $uuid to $id
    public function destroy(string $id)
    {
        $this->customerService->deleteCustomer($id);

        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}