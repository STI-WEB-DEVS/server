<?php

namespace App\Http\Controllers;

use App\Service\CustomerService;
use App\Http\Requests\CustomerRequest;
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
        return $this->customerService->listCustomer(
            $request->input('per_page', 15)
        );
    }

    /**
     * Output #1 — Create Customer (with validation)
     */
    public function store(CustomerRequest $request)
    {
        return $this->customerService->createCustomer(
            $request->validated()
        );
    }

    public function show(string $uuid)
    {
        return $this->customerService->getCustomer($uuid);
    }

    public function update(CustomerRequest $request, string $uuid)
    {
        return $this->customerService->updateCustomer(
            $uuid,
            $request->validated()
        );
    }

    public function destroy(string $uuid)
    {
        $this->customerService->deleteCustomer($uuid);
        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully.',
        ], 200);
    }

    public function restore(string $uuid)
    {
        return $this->customerService->restoreCustomer($uuid);
    }

    /**
     * Output #3 — Orders list per customer
     */
    public function orders(string $uuid)
    {
        return $this->customerService->getCustomerWithOrders($uuid);
    }
}