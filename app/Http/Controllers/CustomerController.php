<?php

namespace App\Http\Controllers;

use App\Service\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    private CustomerService $customer_service;
    public function __construct(CustomerService $customerService)
    {
        $this->customer_service =$customerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->customer_service->getCustomers();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
