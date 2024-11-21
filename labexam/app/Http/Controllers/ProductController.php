<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $products = Product::all();
            return response()->json($products, 200);
        }
        return view('product', ['products' => Product::all()]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_name' => 'required|string|max:100',
            'category_name' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:100',
            'selling_price' => 'nullable|numeric|min:0',
        ]);

        $product = Product::create($validatedData);

        return response()->json([
            'message' => 'Product created successfully!',
            'product' => $product
        ], 201);
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product, 200);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validatedData = $request->validate([
            'product_name' => 'required|string|max:100',
            'category_name' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:100',
            'selling_price' => 'nullable|numeric|min:0',
        ]);

        $product->update($validatedData);

        return response()->json(['message' => 'Product updated successfully!', 'product' => $product], 200);
    }



    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
