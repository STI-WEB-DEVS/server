<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
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

        return response()->json([
            'controller_message' => 'Success in Controller.',
            'service_message' => $result['service_message'],
        ], 200);
    }

    public function store(CustomerStoreRequest $request)
    {
        $result = $this->customerService->createCustomer($request->all());

        return response()->json([
            'controller_message' => 'Success in Controller.',
            'request_message' => 'Success in Request.',
        ], 200);
    }

    public function show(string $customer)
    {
        $result = $this->customerService->getCustomer($customer);

        return response()->json([
            'controller_message' => 'Success in Controller.',
            'service_message' => $result['service_message'],
        ], 200);

    }

    public function update(CustomerStoreRequest $request, string $customer)
    {
        $result = $this->customerService->updateCustomer($customer, $request->all());

        return response()->json([
            'message' => 'Update successful',
        ], 200);

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