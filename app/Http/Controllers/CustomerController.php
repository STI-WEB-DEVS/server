<?php

namespace App\Http\Controllers;
use App\Service\CustomerService;
use Illuminate\Http\Request;

use App\Http\Requests\CustomerStoreRequest;
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
        return $this->customerService->getCompany($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        return $this->customerService->customerUpdate($request->all(),$id);
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
