<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Service\CustomerService;
use Illuminate\Http\Request;

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

    public function store(CustomerStoreRequest $request)
    {
        return $this->customerService->createCustomer($request->validated());
    }

    public function show(string $uuid)
    {
        return $this->customerService->getCustomer($uuid);
    }

    public function update(CustomerStoreRequest $request, string $uuid)
    {
        return $this->customerService->updateCustomer($uuid, $request->validated());
    }

    public function destroy(string $uuid)
    {
        $this->customerService->deleteCustomer($uuid);
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}
