<?php

namespace App\Service;

// use App\Http\Resources\LanguageResource;
// use App\Repository\LanguageRepository;

use App\Models\Customer;

class CustomerService
{
    public function getCustomers()
    {
        $customers = Customer::paginate(request()->input('per_page', 10));

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
}

