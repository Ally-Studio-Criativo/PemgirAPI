<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProductImage::query();

        // Filter by product
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $images = $query->orderBy('order')->get();

        return response()->json($images);
    }

    /**
     * Store a newly created resource in storage (upload image).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'order' => 'integer',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Store image
        $image = $request->file('image');
        $filename = time() . '_' . $image->getClientOriginalName();
        $path = $image->storeAs('products/' . $product->id, $filename, 'public');

        // Create image record
        $productImage = ProductImage::create([
            'product_id' => $validated['product_id'],
            'filename' => $filename,
            'path' => '/storage/' . $path,
            'order' => $validated['order'] ?? 0,
        ]);

        return response()->json($productImage, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $image = ProductImage::with('product')->findOrFail($id);

        return response()->json($image);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $image = ProductImage::findOrFail($id);

        $validated = $request->validate([
            'order' => 'sometimes|integer',
        ]);

        $image->update($validated);

        return response()->json($image);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $image = ProductImage::findOrFail($id);

        // Delete file from storage
        $filePath = str_replace('/storage/', '', $image->path);
        Storage::disk('public')->delete($filePath);

        $image->delete();

        return response()->json(['message' => 'Image deleted successfully'], 200);
    }
}
