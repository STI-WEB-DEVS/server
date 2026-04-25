<?php

namespace App\Http\Controllers;
use App\Service\CustomerService;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index()
    {
        return $this->customerService->listCustomer();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email', // This checks if email exists
        ]);
    
        // If validation passes, proceed to your service
        return $this->customerService->createCustomer($validated);
    }


    public function show(string $id)
    {
        return $this->customerService->getCustomer($uuid);
    }


    public function update(Request $request, string $id)
    {
        return $this->customerService->updateCustomer($uuid, $request->all());
    }

    
    public function destroy(string $id)
    {
        $this->customerService->deleteCompany($uuid);

        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}
