<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ColorPalette;
use Illuminate\Http\Request;

class ColorPaletteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ColorPalette::with('colors');

        // Filter by active status if provided
        if ($request->has('active')) {
            $query->where('active', $request->active);
        }

        $palettes = $query->orderBy('year', 'desc')->get();

        // Filter out palettes without colors if requested
        if ($request->has('with_colors') && $request->with_colors) {
            $palettes = $palettes->filter(function ($palette) {
                return $palette->colors->count() > 0;
            })->values();
        }

        return response()->json($palettes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:2020|max:2100',
            'order' => 'integer',
        ]);

        $palette = ColorPalette::create($validated);

        return response()->json($palette, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $palette = ColorPalette::with('colors')->findOrFail($id);

        return response()->json($palette);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $palette = ColorPalette::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'year' => 'sometimes|integer|min:2020|max:2100',
            'order' => 'sometimes|integer',
        ]);

        $palette->update($validated);

        return response()->json($palette);
    }

    /**
     * Remove the specified resource from storage.
     * If palette has colors, it will be inactivated instead of deleted.
     */
    public function destroy(string $id)
    {
        $palette = ColorPalette::findOrFail($id);

        // Check if palette has colors
        if ($palette->colors()->count() > 0) {
            // Inactivate instead of delete
            $palette->update(['active' => false]);
            return response()->json([
                'message' => 'Palette has colors and was inactivated',
                'inactivated' => true
            ], 200);
        }

        // Delete if no colors
        $palette->delete();
        return response()->json([
            'message' => 'Palette deleted successfully',
            'inactivated' => false
        ], 200);
    }
}
