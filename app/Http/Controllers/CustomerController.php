<?php

namespace App\Http\Controllers;
use App\Service\CustomerService;
use App\Models\Customer;

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
        ]);
        
        $customer = Customer::create($validated);

        // Automatically create a user account and assign the role correctly
        $user = \App\Models\User::create([
            'customer_id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'password' => bcrypt('password'), // Default password
        ]);

        $user->assignRole('customer');

        return response()->json($customer, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return response()->json($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
        ]);

        $customer->update($validated);

        // Automatically sync the user account details and roles
        $user = \App\Models\User::where('customer_id', $customer->id)->first();
        if ($user) {
            $user->update([
                'name' => $customer->name,
                'email' => $customer->email,
            ]);
            $user->syncRoles(['customer']);
        }

        return response()->json($customer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(null, 204);
    }
}
