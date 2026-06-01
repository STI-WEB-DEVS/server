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
            $validated = $request->validate([
                'name' => 'required|string',
                'price' => 'required|numeric|min:0',
            ]);

            $product = Product::findOrFail($id);
            $product->update($validated);
            
            return response()->json([
                'message' => 'Product updated successfully',
                'data' => $product
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
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

            // Check if product is referenced in order_items
            $orderItemsCount = \DB::table('order_items')->where('product_id', $id)->count();
            
            if ($orderItemsCount > 0) {
                return response()->json([
                    'message' => 'Cannot delete this product because it has been ordered by customers. Consider marking it as unavailable instead.',
                    'error' => 'FOREIGN_KEY_CONSTRAINT'
                ], 409); // 409 Conflict
            }

            $product->delete();
            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle any other database constraint violations
            if ($e->getCode() == '23000') {
                return response()->json([
                    'message' => 'Cannot delete this product because it is referenced in existing orders.',
                    'error' => 'FOREIGN_KEY_CONSTRAINT'
                ], 409);
            }
            return response()->json(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}