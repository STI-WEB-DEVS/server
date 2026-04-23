<?php

namespace App\Service;

// use App\Http\Resources\LanguageResource;
// use App\Repository\LanguageRepository;

use App\Models\Product;

class ProductService
{
    public function getProducts()
    {
        $products = Product::paginate(request()->input('per_page', 10));

        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'total' => $products->total(),
                'from' => $products->firstItem() ?? 0,
                'to' => $products->lastItem() ?? 0,
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ]
        ]);
    }
}

