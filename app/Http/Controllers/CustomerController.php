<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service\CustomerService;

class CustomerController extends Controller
{
    private CustomerService $customerservice;
    public function __construct(CustomerService $customerservice)
    {
        $this->customerservice = $customerservice;
    }

    public function index()
    {
        return response()->json($this->customerservice->listCustomers());
    }

    public function store(Request $request)
    {

    }

    public function show(string $id)
    {

    }

    public function update(Request $request, string $id)
    {

    }

    public function destroy(string $id)
    {

    }
}
