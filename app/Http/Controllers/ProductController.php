<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index() 
    {
        return response()->json(Product::all());
    }

    public function store(Request $request) 
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'price' => 'required|numeric',
            ]);

            // Add the UUID required by your database
            $validated['uuid'] = (string) Str::uuid();

            $product = Product::create($validated);
            return response()->json($product, 201);
        } catch (\Exception $e) {
            // Returns the specific error (e.g., fillable or database error)
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id) 
    {
        try {
            $product = Product::findOrFail($id);
            $product->update($request->only(['name', 'price']));
            return response()->json($product);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id) 
    {
        try {
            // Find the product by the integer ID seen in your DB
            $product = Product::find($id);
            
            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            $product->delete();
            return response()->json(['message' => 'Deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}