<?php

namespace App\Http\Controllers;
use App\Service\CustomerService;
use GrahamCampbell\ResultType\Success;

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
            return $this->customerService->listCustomer($request->input('per_page', 15));
        }
     
    public function store(Request $request)
    {
        return $this->customerService->createCustomers($request->all());
    }

   
    public function show(string $uuid)
    {
        return $this->customerService->getCustomer($uuid);
    }

    
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

        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}
