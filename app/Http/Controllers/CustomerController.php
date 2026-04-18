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
        return $this->customerService->createCustomer($request->all());
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
