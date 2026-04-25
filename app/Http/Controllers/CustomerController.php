<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Http\Resources\CustomerResource;
use App\Service\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $service;

    public function __construct(CustomerService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $customers = $this->service->getAllCustomers();
        return CustomerResource::collection($customers);
    }

    public function store(CustomerStoreRequest $request)
    {
        $customer = $this->service->registerCustomer($request->validated());
        return new CustomerResource($customer);
    }

    public function show(string $id)
    {
        $customer = $this->service->getCustomerById($id);
        return new CustomerResource($customer);
    }

    public function update(Request $request, string $id)
    {
        return $this->customerService->updateCustomer($uuid, $request->all());
    }

    public function destroy(string $id)
    {
        $this->customerService->deleteCustomer($uuid);

        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}