<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Color::with('palette');

        // Filter by palette_id if provided
        if ($request->has('palette_id')) {
            $query->where('palette_id', $request->palette_id);
        }

        $colors = $query->orderBy('palette_id', 'desc')
            ->orderBy('order')
            ->get();

        return response()->json($colors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'palette_id' => 'required|exists:color_palettes,id',
            'ref' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'order' => 'integer',
        ]);

        // Upload image
        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $filename = 'color_temp_' . time() . '.' . $extension;

            $publicPath = public_path('images/colors');
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }

            $request->file('image')->move($publicPath, $filename);
            $validated['image'] = "images/colors/{$filename}";
        }

        $color = Color::create($validated);

        // Rename file with actual color ID
        if (isset($validated['image'])) {
            $oldPath = public_path($validated['image']);
            $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
            $newFilename = "color_{$color->id}.{$extension}";
            $newPath = public_path("images/colors/{$newFilename}");

            if (file_exists($oldPath)) {
                rename($oldPath, $newPath);
                $color->update(['image' => "images/colors/{$newFilename}"]);
            }
        }

        return response()->json($color, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $color = Color::findOrFail($id);

        return response()->json($color);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $color = Color::findOrFail($id);

        $validated = $request->validate([
            'palette_id' => 'sometimes|exists:color_palettes,id',
            'ref' => 'sometimes|nullable|string|max:255',
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'type' => 'sometimes|nullable|string|max:255',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,webp|max:5120',
            'order' => 'sometimes|integer',
        ]);

        // Upload new image if provided
        if ($request->hasFile('image')) {
            // Delete old image
            if ($color->image) {
                $oldImagePath = public_path($color->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $extension = $request->file('image')->getClientOriginalExtension();
            $filename = "color_{$color->id}.{$extension}";

            $publicPath = public_path('images/colors');
            $request->file('image')->move($publicPath, $filename);
            $validated['image'] = "images/colors/{$filename}";
        }

        $color->update($validated);

        return response()->json($color);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $color = Color::findOrFail($id);

        // Delete image file
        if ($color->image) {
            $imagePath = public_path($color->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $color->delete();

        return response()->json(['message' => 'Color deleted successfully'], 200);
    }
}
