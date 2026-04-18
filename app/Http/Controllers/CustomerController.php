<?php

namespace App\Http\Controllers;
<<<<<<< HEAD

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $customers = Customer::paginate($request->input('per_page', 10));
        return response()->json([
            'data' => $customers->items(),
            'meta' => [
                'total' => $customers->total(),
                'from' => $customers->firstItem() ?? 0,
                'to' => $customers->lastItem() ?? 0,
                'per_page' => $customers->perPage(),
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        return Customer::create([
            'uuid' => (string) Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
        ]);
    }

    public function show(Customer $customer)
    {
        return $customer;
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $customer->update($request->only('name', 'email'));
        return $customer;
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->noContent();
=======
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
        return $this->customerService->getCustomers();
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
>>>>>>> 719b480669c1af01f0bbc69fd037eb8590741e5f
    }
}
