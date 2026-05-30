<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private const PRODUCT_NAME_REGEX = "/^[A-Za-z0-9 '-]+$/";

    public function index()
    {
        $products = Product::paginate(15);
        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'regex:' . self::PRODUCT_NAME_REGEX, 'max:255', 'unique:products'],
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ], [
            'name.regex' => 'Product name cannot contain special characters.',
            'name.unique' => 'Product name already exists.',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::where('uuid', $id)->firstOrFail();
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('uuid', $id)->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'regex:' . self::PRODUCT_NAME_REGEX, 'max:255', 'unique:products,name,' . $product->id],
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'restock_quantity' => 'nullable|integer|min:0',
        ], [
            'name.regex' => 'Product name cannot contain special characters.',
            'name.unique' => 'Product name already exists.',
        ]);

        // Restock logic: Current Stock + Restock Quantity
        if ($request->filled('restock_quantity')) {
            $validated['stock_quantity'] = $product->stock_quantity + $request->restock_quantity;
        }

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::where('uuid', $id)->firstOrFail();
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
