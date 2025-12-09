<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Display a listing of images for a product.
     */
    public function index(string $productId)
    {
        $product = Product::findOrFail($productId);
        $images = $product->images()->orderBy('order')->get();

        return response()->json($images);
    }

    /**
     * Store a newly uploaded image for a product.
     */
    public function store(Request $request, string $productId)
    {
        try {
            $product = Product::findOrFail($productId);

            $validated = $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max
                'ref' => 'nullable|string|max:255',
                'color_name' => 'nullable|string|max:255',
                'image_type' => 'nullable|string|max:255',
                'order' => 'integer|min:0',
            ]);

            // Get next order if not provided
            if (!isset($validated['order'])) {
                $maxOrder = $product->images()->max('order') ?? -1;
                $validated['order'] = $maxOrder + 1;
            }

            // Generate filename
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename = "product_{$product->id}_img_{$validated['order']}.{$extension}";

            // Save directly to public/images/products/
            $publicPath = public_path('images/products');
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $request->file('image')->move($publicPath, $filename);
            $path = "images/products/{$filename}";

            $image = ProductImage::create([
                'product_id' => $product->id,
                'ref' => $validated['ref'] ?? null,
                'color_name' => $validated['color_name'] ?? null,
                'image_type' => $validated['image_type'] ?? null,
                'filename' => $filename,
                'path' => $path,
                'order' => $validated['order'],
            ]);

            return response()->json($image, 201);
        } catch (\Exception $e) {
            \Log::error('Error uploading image', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error uploading image',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an image's metadata.
     */
    public function update(Request $request, string $productId, string $imageId)
    {
        $product = Product::findOrFail($productId);
        $image = ProductImage::where('product_id', $product->id)
            ->where('id', $imageId)
            ->firstOrFail();

        $validated = $request->validate([
            'ref' => 'sometimes|nullable|string|max:255',
            'color_name' => 'sometimes|nullable|string|max:255',
            'image_type' => 'sometimes|nullable|string|max:255',
            'order' => 'sometimes|integer|min:0',
        ]);

        $image->update($validated);

        return response()->json($image);
    }

    /**
     * Reorder images.
     */
    public function reorder(Request $request, string $productId)
    {
        $product = Product::findOrFail($productId);

        $validated = $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|exists:product_images,id',
            'images.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['images'] as $imageData) {
            ProductImage::where('id', $imageData['id'])
                ->where('product_id', $product->id)
                ->update(['order' => $imageData['order']]);
        }

        return response()->json([
            'message' => 'Images reordered successfully',
            'images' => $product->images()->orderBy('order')->get()
        ]);
    }

    /**
     * Remove an image.
     */
    public function destroy(string $productId, string $imageId)
    {
        $product = Product::findOrFail($productId);
        $image = ProductImage::where('product_id', $product->id)
            ->where('id', $imageId)
            ->firstOrFail();

        // Delete image file from public directory
        if ($image->path) {
            $fullPath = public_path($image->path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        $image->delete();

        return response()->json(['message' => 'Image deleted successfully'], 200);
    }
}
