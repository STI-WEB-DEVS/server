<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\CustomerService;
use App\Service\CustomerRepository;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Resources\CustomerResource;

class CustomerController extends Controller
{
    private CustomerService $customerService;


   public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index()
    {
        $customers = $this->customerService->getAllCustomers();
        return CustomerResource::collection($customers);
    }

    public function store(CustomerStoreRequest $request) 
    {
        $customer = $this->customerService->createCustomer($request->validated());
        return new CustomerResource($customer);
    }

    public function show(string $id)
    {
        $customer = $this->customerService->getCustomerById($id);
        return new CustomerResource($customer);
    }

    public function update(Request $request, string $id)
    {
        $customer = $this->customerService->updateCustomer($id, $request->validated());
        return new CustomerResource($customer);
    }

    public function destroy(string $id)
    {
        $this->customerService->deleteCustomer($id);
        return response()->json(['message' => 'Customer deleted successfully'], 200);
    }
}
