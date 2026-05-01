<?php

namespace App\Http\Controllers;

use App\Service\CustomerService;
use App\Http\Requests\CustomerStoreRequest;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    public CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function customerOrders(string $uuid)
    {
        return $this->customerService->getCustomerOrders($uuid);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        return $this->customerService->getCustomers();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerStoreRequest $request)
    {
        //
        return $this->customerService->createCustomer($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        return $this->customerService->retrieveCustomer($id);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        return $this->customerService->updateCustomer($request->all(), $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //

        return $this->customerService->deleteCustomer($id);
    }
}
