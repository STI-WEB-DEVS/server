<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
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
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\']+$/'],
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.regex' => 'Product name must contain only letters, spaces, hyphens, and apostrophes.',
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
            'name' => ['sometimes', 'string', 'max:255', 'regex:/^[a-zA-Z\s\-\']+$/'],
            'price' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.regex' => 'Product name must contain only letters, spaces, hyphens, and apostrophes.',
        ]);

        // Remove stock from validated data if present (stock can only be updated via restock endpoint)
        unset($validated['stock']);

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::where('uuid', $id)->firstOrFail();
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    /**
     * Restock a product by adding quantity to existing stock
     */
    public function restock(Request $request, $id)
    {
        $product = Product::where('uuid', $id)->firstOrFail();

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product->increment('stock', $validated['quantity']);

        return response()->json([
            'message' => 'Product restocked successfully',
            'product' => $product->fresh(),
        ]);
    }
}