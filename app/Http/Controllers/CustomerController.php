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

    public function index(Request $request)
    {
        return $this->customerService->listCustomer($request->input('per_page', 15));
    }

    public function store(Request $request)
    {
        try {
            return $this->customerService->createCustomer($request->all());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        return $this->customerService->getCustomer($id);
    }

    public function update(Request $request, string $id)
    {
        return $this->customerService->updateCustomer($id, $request->all());
    }

    public function destroy($id)
    {
        $customer = \App\Models\Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}