<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandingPageText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingPageTextController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $texts = LandingPageText::all();
        return response()->json($texts);
    }

    /**
     * Get texts organized by sections
     */
    public function getBySections()
    {
        $texts = LandingPageText::all()->groupBy('section');
        return response()->json($texts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section' => 'required|string|max:255|unique:landing_page_texts',
            'key' => 'nullable|string|max:255',
            'text_pt' => 'nullable|string',
            'text_en' => 'nullable|string',
            'text_es' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $text = LandingPageText::create($request->all());

        return response()->json([
            'message' => 'Texto criado com sucesso',
            'data' => $text
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $text = LandingPageText::find($id);

        if (!$text) {
            return response()->json([
                'message' => 'Texto não encontrado'
            ], 404);
        }

        return response()->json($text);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $text = LandingPageText::find($id);

        if (!$text) {
            return response()->json([
                'message' => 'Texto não encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'section' => 'sometimes|required|string|max:255|unique:landing_page_texts,section,' . $id,
            'key' => 'nullable|string|max:255',
            'text_pt' => 'nullable|string',
            'text_en' => 'nullable|string',
            'text_es' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $text->update($request->all());

        return response()->json([
            'message' => 'Texto atualizado com sucesso',
            'data' => $text
        ]);
    }

    /**
     * Bulk update texts
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'texts' => 'required|array',
            'texts.*.id' => 'required|exists:landing_page_texts,id',
            'texts.*.text_pt' => 'nullable|string',
            'texts.*.text_en' => 'nullable|string',
            'texts.*.text_es' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->texts as $textData) {
            $text = LandingPageText::find($textData['id']);
            if ($text) {
                $text->update([
                    'text_pt' => $textData['text_pt'] ?? $text->text_pt,
                    'text_en' => $textData['text_en'] ?? $text->text_en,
                    'text_es' => $textData['text_es'] ?? $text->text_es,
                ]);
            }
        }

        return response()->json([
            'message' => 'Textos atualizados com sucesso'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $text = LandingPageText::find($id);

        if (!$text) {
            return response()->json([
                'message' => 'Texto não encontrado'
            ], 404);
        }

        $text->delete();

        return response()->json([
            'message' => 'Texto removido com sucesso'
        ]);
    }
}
