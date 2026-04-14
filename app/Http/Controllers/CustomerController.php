<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use App\Service\CustomerService;

class CustomerController extends Controller
{
   
    public function __construct(CustomerService $customerRequest)
    {
        $this->customerRequest = $customerRequest;
    }


    public function index(Request $request)
    {
        return $this->customerRequest->listCustomer($request->input('per_page', 15));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerRequest $request)
    {
        // Check if a customer with the same email OR name already exists
        $existing = \App\Models\Customer::where('email', $request->email)
            ->orWhere('name', $request->name)
            ->first();
    
        if ($existing) {
            return response()->json([
                'message' => 'Customer with this email or name already exists'
            ], 409); // 409 Conflict
        }
    
        // If no duplicate, create the customer
        return $this->customerRequest->createCustomer($request->all());
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        return $this->customerRequest->getCustomer($uuid);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        return $this->customerRequest->updateCustomer($uuid, $request->all());
    }

    public function destroy(string $uuid)
    {
        $customer = \App\Models\Customer::where('uuid', $uuid)->first();
    
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
    
        $customer->delete();
    
        return response()->json(['message' => 'Deleted successfully'], 200);
    }
    
    
}
