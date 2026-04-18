<?php

namespace App\Http\Controllers;

use App\Service\CustomerService;
use App\Repository\CustomerRepository;
use App\Http\Requests\CustomerStoreRequest;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public CustomerService $customerService;


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
        $customer = $this->customerService->getCustomer($id);
        return new CustomerResource($customer);
    }

    public function update(CustomerStoreRequest $request, string $id)
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
