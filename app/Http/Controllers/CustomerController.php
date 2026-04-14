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

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $customers = $this->customerService->listCustomer($request->input('per_page', 15));
        return response()->json($customers, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $customer = $this->customerService->createCustomer($request->all());
        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $customer
        ], 201); // 201 is the standard status for "Created"
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid): JsonResponse
    {
        $customer = $this->customerService->getCustomer($uuid);
        return response()->json($customer, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid): JsonResponse
    {
        $customer = $this->customerService->updateCustomer($uuid, $request->all());
        return response()->json([
            'message' => 'Customer updated successfully',
            'data' => $customer
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid): JsonResponse
    {
        $this->customerService->deleteCustomer($uuid);
        return response()->json(['message' => 'Customer deleted successfully'], 200);
    }

    /**
     * Restore a soft-deleted resource.
     */
    public function restore(string $uuid): JsonResponse
    {
        $customer = $this->customerService->restoreCustomer($uuid);
        return response()->json([
            'message' => 'Customer restored successfully',
            'data' => $customer
        ], 200);
    }
}