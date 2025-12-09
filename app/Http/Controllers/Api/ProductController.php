<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images']);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->orderBy('order')->get();

        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
            'composition' => 'nullable|string',
            'gramatura' => 'nullable|integer',
            'width' => 'nullable|string|max:255',
            'yield' => 'nullable|string|max:255',
            'order' => 'integer',
        ]);

        $product = Product::create($validated);

        return response()->json($product->load(['category', 'images']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['category', 'images'])->findOrFail($id);

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'reference' => 'nullable|string|max:255',
            'composition' => 'nullable|string',
            'gramatura' => 'nullable|integer',
            'width' => 'nullable|string|max:255',
            'yield' => 'nullable|string|max:255',
            'order' => 'sometimes|integer',
        ]);

        $product->update($validated);

        return response()->json($product->load(['category', 'images']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
