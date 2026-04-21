<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Service\CustomerService;


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
        // return "check";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $accountExists = Customer::where('email', $request->email)->exists();

        if ($accountExists) {
            return response()->json(['message' => 'Already exists'], 409);
        }

        return $this->customerService->createCustomer($request->all());
        // if($request){
        //     return response()->json(['message' => 'Already exists'], 409);
        // }
        // return $this->customerService->createCustomer($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        return $this->customerService->getCustomer($uuid);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        return $this->customerService->updateCustomer($uuid, $request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $this->customerService->deleteCustomer($uuid);

        return response()->json(['message' => 'Deleted successfully'], 200);    }
}
