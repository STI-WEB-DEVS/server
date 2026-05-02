<?php

namespace App\Http\Controllers;
use App\Http\Requests\CustomerStoreRequest;
use Illuminate\Http\Request;
use App\Service\CustomerService;

class CustomerController extends Controller
{

    private CustomerService $customerService;

    public function __construct(CustomerService $customerService){
        $this->customerService = $customerService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return $this->customerService->paguinate();
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
       return $this->customerService->getCustomer($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerStoreRequest $request, string $id)
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
    public function customerOrders(string $id)
    {
        //

        return $this->customerService->getCustomerO+rders($id);
    }


}
