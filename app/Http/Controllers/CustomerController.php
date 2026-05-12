<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Http\Resources\CustomerResource;
use App\Service\CustomerService;
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
        $result = $this->customerService->listCustomer($request->input('per_page', 15));

        return CustomerResource::collection($result['data']);
    }

    public function store(CustomerStoreRequest $request)
    {
        $result = $this->customerService->createCustomer($request->all());

        return (new CustomerResource($result['data']))->response()->setStatusCode(201);
    }

    public function show(string $customer)
    {
        $result = $this->customerService->getCustomer($customer);

        return new CustomerResource($result['data']);
    }

    public function update(CustomerStoreRequest $request, string $customer)
    {
        $result = $this->customerService->updateCustomer($customer, $request->all());

        return new CustomerResource($result['data']);
    }

    public function destroy(string $customer)
    {
        $result = $this->customerService->deleteCustomer($customer);

        if (! $result['deleted']) {
            return response()->json(['message' => 'No UUID record matches found.'], 404);
        }

        return response()->json(['message' => 'Deleted Successfully'], 200);
    }
}