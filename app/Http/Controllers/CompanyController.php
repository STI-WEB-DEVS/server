<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
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
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        return $this->customerService->listCustomer($request->input('per_page', 15));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(CustomerStoreRequest $request)
    {
        return $this->customerService->createCustomer($request->all());
    }

    /**
     * Display the specified customer.
     */
    public function show(string $uuid)
    {
        return $this->customerService->getCustomer($uuid);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, string $uuid)
    {
        return $this->customerService->updateCustomer($uuid, $request->all());
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(string $uuid): JsonResponse
    {
        $this->customerService->deleteCustomer($uuid);

        return response()->json(['message' => 'Deleted successfully'], 200);
    }

    /**
     * Restore a soft-deleted customer.
     */
    public function restore(string $uuid)
    {
        return $this->customerService->restoreCustomer($uuid);
    }
}