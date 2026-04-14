<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Service\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // This logic produces the exact output in your screenshot
        return response()->json([
            'message' => 'Successful Post!',
            'received_data' => $request->all()
        ], 201);
    }

    public function index()
    {
        return response()->json(['message' => 'Success']);
    }
}