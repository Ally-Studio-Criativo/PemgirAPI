<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

            // Log comprehensive request details for debugging
            \Log::info('ProductImage upload attempt', [
                'product_id' => $productId,
                'has_file' => $request->hasFile('image'),
                'file_size' => $request->hasFile('image') ? $request->file('image')->getSize() : null,
                'file_size_kb' => $request->hasFile('image') ? round($request->file('image')->getSize() / 1024, 2) . 'KB' : null,
                'mime_type' => $request->hasFile('image') ? $request->file('image')->getMimeType() : null,
                'original_name' => $request->hasFile('image') ? $request->file('image')->getClientOriginalName() : null,
                'error' => $request->hasFile('image') ? $request->file('image')->getError() : null,
                'error_message' => $request->hasFile('image') ? $request->file('image')->getErrorMessage() : null,
                'is_valid' => $request->hasFile('image') ? $request->file('image')->isValid() : null,
                'all_files' => array_keys($request->allFiles()),
                'all_input_keys' => array_keys($request->all()),
                'content_length' => $request->header('Content-Length'),
                'content_type' => $request->header('Content-Type'),
                'php_upload_max' => ini_get('upload_max_filesize'),
                'php_post_max' => ini_get('post_max_size'),
                'php_max_file_uploads' => ini_get('max_file_uploads'),
                'php_memory_limit' => ini_get('memory_limit'),
            ]);

            // Validate request
            $validated = $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB max
                'ref' => 'nullable|string|max:255',
                'color_name' => 'nullable|string|max:255',
                'image_type' => 'nullable|string|max:255',
                'in_2027_palette' => 'boolean',
                'has_cuff_collar' => 'nullable|string',
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

            // Save to storage/app/public/images/products/
            $storagePath = storage_path('app/public/images/products');

            // Check if directory exists and is writable
            if (!file_exists($storagePath)) {
                \Log::info('Creating directory', ['path' => $storagePath]);
                if (!mkdir($storagePath, 0755, true)) {
                    throw new \Exception("Failed to create directory: {$storagePath}");
                }
            }

            if (!is_writable($storagePath)) {
                throw new \Exception("Directory is not writable: {$storagePath}");
            }

            // Move uploaded file
            \Log::info('Moving file', ['from' => $request->file('image')->getRealPath(), 'to' => $storagePath . '/' . $filename]);
            if (!$request->file('image')->move($storagePath, $filename)) {
                throw new \Exception("Failed to move uploaded file to: {$storagePath}/{$filename}");
            }

            $path = "storage/images/products/{$filename}";

            $image = ProductImage::create([
                'product_id' => $product->id,
                'ref' => $validated['ref'] ?? null,
                'color_name' => $validated['color_name'] ?? null,
                'image_type' => $validated['image_type'] ?? null,
                'in_2027_palette' => $validated['in_2027_palette'] ?? false,
                'has_cuff_collar' => $validated['has_cuff_collar'] ?? null,
                'filename' => $filename,
                'path' => $path,
                'order' => $validated['order'],
            ]);

            \Log::info('Image uploaded successfully', ['image_id' => $image->id]);

            return response()->json($image, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error uploading image', [
                'product_id' => $productId,
                'errors' => $e->errors(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error uploading image', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
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
            'in_2027_palette' => 'sometimes|boolean',
            'has_cuff_collar' => 'sometimes|nullable|string',
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

        // Delete image file from storage
        if ($image->path) {
            // Remove 'storage/' prefix to get actual file path
            $filePath = str_replace('storage/', '', $image->path);
            $fullPath = storage_path('app/public/' . $filePath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        $image->delete();

        return response()->json(['message' => 'Image deleted successfully'], 200);
    }
}
