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
        $query = Product::with(['category', 'categories', 'images']);

        // Filter by category (suporta múltiplas categorias)
        if ($request->has('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Filter by launch status
        if ($request->has('is_launch')) {
            $query->where('is_launch', $request->boolean('is_launch'));
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
            'category_id' => 'nullable|exists:categories,id', // Mantido para compatibilidade
            'category_ids' => 'required|array|min:1', // Novo campo para múltiplas categorias
            'category_ids.*' => 'exists:categories,id',
            'name' => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
            'composition' => 'nullable|string',
            'gramatura' => 'nullable|integer',
            'width' => 'nullable|string|max:255',
            'yield' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'order' => 'integer',
            'is_launch' => 'boolean',
        ]);

        // Separar category_ids antes de criar
        $categoryIds = $validated['category_ids'];
        unset($validated['category_ids']);

        // Manter primeira categoria em category_id para compatibilidade
        if (!isset($validated['category_id']) && !empty($categoryIds)) {
            $validated['category_id'] = $categoryIds[0];
        }

        $product = Product::create($validated);

        // Sincronizar categorias na tabela pivot
        $product->categories()->sync($categoryIds);

        return response()->json($product->load(['category', 'categories', 'images']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['category', 'categories', 'images'])->findOrFail($id);

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'sometimes|nullable|exists:categories,id',
            'category_ids' => 'sometimes|array|min:1',
            'category_ids.*' => 'exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'reference' => 'nullable|string|max:255',
            'composition' => 'nullable|string',
            'gramatura' => 'nullable|integer',
            'width' => 'nullable|string|max:255',
            'yield' => 'nullable|string|max:255',
            'observations' => 'nullable|string',
            'order' => 'sometimes|integer',
            'is_launch' => 'sometimes|boolean',
        ]);

        // Se category_ids foi enviado, sincronizar categorias
        if (isset($validated['category_ids'])) {
            $categoryIds = $validated['category_ids'];
            unset($validated['category_ids']);

            // Atualizar category_id com a primeira categoria para compatibilidade
            if (!empty($categoryIds)) {
                $validated['category_id'] = $categoryIds[0];
            }

            $product->categories()->sync($categoryIds);
        }

        $product->update($validated);

        return response()->json($product->load(['category', 'categories', 'images']));
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
