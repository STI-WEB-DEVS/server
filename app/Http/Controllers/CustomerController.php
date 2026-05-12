<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Service\CustomerService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(private CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        $result = $this->customerService->listCustomer($request->input('per_page', 15));

        return response()->json($result, 200);
    }

    public function store(CustomerStoreRequest $request)
    {
        return response()->json($this->customerService->createCustomer($request->validated()), 201);
    }

    public function show(string $uuid)
    {
        return $this->customerService->getCustomer($uuid);
    }

    public function update(Request $request, string $uuid)
    {
        $this->customerService->updateCustomer($uuid, $request->all());

        return response()->json(['message' => 'Updated successfully'], 200);
    }

    public function destroy(string $uuid)
    {
        try {
            $this->customerService->deleteCustomer($uuid);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'UUID does not exist.'], 404);
        }

        return response()->json(['message' => 'Deleted successfully'], 200);
    }
}