<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::paginate(15);
        return response()->json([
            'data' => $customers->items(),
            'meta' => [
                'current_page' => $customers->currentPage(),
                'from' => $customers->firstItem(),
                'to' => $customers->lastItem(),
                'total' => $customers->total(),
                'per_page' => $customers->perPage(),
                'last_page' => $customers->lastPage(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers',
        ]);

        $customer = Customer::create($validated);

        return response()->json($customer, 201);
    }

    public function show($id)
    {
        $customer = Customer::where('uuid', $id)->firstOrFail();
        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::where('uuid', $id)->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:customers,email,' . $customer->id,
        ]);

        $customer->update($validated);

        return response()->json($customer);
    }

    public function destroy($id)
    {
        $customer = Customer::where('uuid', $id)->firstOrFail();
        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }
}