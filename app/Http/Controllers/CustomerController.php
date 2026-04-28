<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\CustomerService;

class CustomerController extends Controller
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customersService)
    {
        $this->customerService = $customersService;
    }

    public function index()
    {
        return $this->customerService->listCustomers();
    }

    public function store(Request $request)
{
    // 1. Validate the data (Crucial for security and logic)
    $validatedData = $request->validate([
        'email' => 'required|email|unique:customers,email',
        'name'  => 'required|string',
    ]);

    // 2. Pass the validated data to the service
    $customer = $this->customerService->createCustomer($validatedData);

    return response()->json($customer, 201);
}

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        return $this->customerService->getCustomer($uuid);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        return $this->customerService->updateCustomer($uuid, $request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $this->customerService->deleteCustomer($uuid);
        return response()->json(['message' => 'Delete successfully'], 200);
    }
}
