<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
       $this->customerService = $customerService;
    }

    public function index()
    {
        $customers = $this->customerService->getAllCustomers();
        // Return a collection of resources
        return CustomerResource::collection($customers);
        
    }

    public function store(CustomerStoreRequest $request)
    {
        $customer = $this->customerService->createCustomer($request->validated());
        // Return the single newly created resource
        return new CustomerResource($customer);
    }

    public function show($id)
    {
        $customer = $this->customerService->getCustomerById($id);
        return new CustomerResource($customer);
    }

    public function update(Request $request, $id)
    {
        $customer = $this->customerService->updateCustomer($id, $request->all());
        return new CustomerResource($customer);
    }
    

    public function destroy($id)
    {
        $this->customerService->deleteCustomer($id);
        return response()->json(['message' => 'Customer deleted successfully']);
    }
    
}