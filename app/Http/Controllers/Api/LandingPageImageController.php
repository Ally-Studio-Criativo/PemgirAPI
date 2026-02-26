<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LandingPageImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LandingPageImageController extends Controller
{
    /**
     * Get all landing page images
     */
    public function index()
    {
        $images = LandingPageImage::orderBy('id')->get();

        \Log::info('Landing Images API', [
            'count' => $images->count(),
            'images' => $images->map(fn($img) => [
                'type' => $img->type,
                'filename' => $img->filename
            ])
        ]);

        return response()->json($images);
    }

    /**
     * Upload and update landing page image
     */
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:20480', // 20MB max
            'type' => 'required|string|in:lancamentos,produtos,cartela,instagram,prancheta1,prancheta2'
        ]);

        $type = $validated['type'];
        $imageRecord = LandingPageImage::where('type', $type)->first();

        if (!$imageRecord) {
            return response()->json(['error' => 'Tipo de imagem não encontrado'], 404);
        }

        // Gerar nome único com timestamp para evitar cache
        $uploadedFile = $request->file('image');
        $extension = $uploadedFile->getClientOriginalExtension();
        $timestamp = time();
        $baseName = preg_replace('/[^A-Za-z0-9]/', '_', $type);
        $filename = "{$baseName}_{$timestamp}.{$extension}";

        // Caminho do diretório public/images
        $publicPath = public_path('images');

        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0755, true);
        }

        // Deletar a imagem antiga antes de salvar a nova (evita problemas de cache)
        if ($imageRecord->filename) {
            $oldPath = $publicPath . '/' . $imageRecord->filename;
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        // Mover novo arquivo
        $newSize = $uploadedFile->getSize();
        $uploadedFile->move($publicPath, $filename);

        // Atualizar banco com filename, path e size
        $newPath = "images/{$filename}";
        $imageRecord->update([
            'filename' => $filename,
            'path' => $newPath,
            'size' => $newSize
        ]);
        $updatedRecord = $imageRecord->fresh();

        \Log::info('Landing Image Uploaded', [
            'type' => $type,
            'old' => $imageRecord->filename,
            'new' => $filename,
            'size' => round($newSize / 1024, 2) . ' KB'
        ]);

        return response()->json([
            'message' => 'Imagem atualizada com sucesso',
            'image' => $updatedRecord
        ]);
    }
}
