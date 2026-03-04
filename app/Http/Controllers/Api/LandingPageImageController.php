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
     * Upload and update landing page image or video
     */
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp|max:10240',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:102400', // 100MB max para vídeos
            'type' => 'required|string|in:lancamentos,produtos,cartela,instagram,prancheta1,prancheta2,hero_video'
        ]);

        $type = $validated['type'];
        $imageRecord = LandingPageImage::where('type', $type)->first();

        if (!$imageRecord) {
            return response()->json(['error' => 'Tipo não encontrado'], 404);
        }

        // Gerar nome único com timestamp para evitar cache
        $uploadedFile = $request->file('image') ?? $request->file('video');

        if (!$uploadedFile) {
            return response()->json(['error' => 'Nenhum arquivo enviado'], 400);
        }

        $extension = $uploadedFile->getClientOriginalExtension();
        $timestamp = time();
        $baseName = preg_replace('/[^A-Za-z0-9]/', '_', $type);
        $filename = "{$baseName}_{$timestamp}.{$extension}";

        // Caminho do diretório apropriado (videos ou images)
        $isVideo = in_array($extension, ['mp4', 'mov', 'avi']);
        $directory = $isVideo ? 'videos' : 'images';
        $storagePath = storage_path("app/public/{$directory}");

        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        // Deletar arquivo antigo antes de salvar novo (evita problemas de cache)
        if ($imageRecord->filename && $imageRecord->path) {
            // Remove 'storage/' prefix to get actual file path
            $filePath = str_replace('storage/', '', $imageRecord->path);
            $oldPath = storage_path('app/public/' . $filePath);
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        // Mover novo arquivo
        $newSize = $uploadedFile->getSize();
        $uploadedFile->move($storagePath, $filename);

        // Atualizar banco com filename, path e size (incluindo storage/ no path)
        $newPath = "storage/{$directory}/{$filename}";
        $imageRecord->update([
            'filename' => $filename,
            'path' => $newPath,
            'size' => $newSize
        ]);
        $updatedRecord = $imageRecord->fresh();

        \Log::info('Landing File Uploaded', [
            'type' => $type,
            'old' => $imageRecord->filename,
            'new' => $filename,
            'size' => round($newSize / 1024, 2) . ' KB',
            'is_video' => $isVideo
        ]);

        return response()->json([
            'message' => ($isVideo ? 'Vídeo' : 'Imagem') . ' atualizado com sucesso',
            'image' => $updatedRecord
        ]);
    }

    /**
     * Update hero video URL (para YouTube/Vimeo)
     */
    public function updateVideoUrl(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|string|url',
            'type' => 'required|string|in:hero_video'
        ]);

        $type = $validated['type'];
        $record = LandingPageImage::where('type', $type)->first();

        if (!$record) {
            return response()->json(['error' => 'Tipo não encontrado'], 404);
        }

        $record->update([
            'path' => $validated['url'],
            'filename' => 'external_video'
        ]);

        return response()->json([
            'message' => 'URL do vídeo atualizada com sucesso',
            'image' => $record->fresh()
        ]);
    }

    /**
     * Delete hero video
     */
    public function deleteVideo($type)
    {
        if ($type !== 'hero_video') {
            return response()->json(['error' => 'Tipo inválido'], 400);
        }

        $record = LandingPageImage::where('type', $type)->first();

        if (!$record) {
            return response()->json(['error' => 'Vídeo não encontrado'], 404);
        }

        // Deletar arquivo físico se existir
        if ($record->path && !filter_var($record->path, FILTER_VALIDATE_URL)) {
            $fullPath = public_path($record->path);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }

        // Limpar registro no banco
        $record->update([
            'path' => '',
            'filename' => 'pending_upload',
            'size' => null
        ]);

        return response()->json([
            'message' => 'Vídeo excluído com sucesso'
        ]);
    }
}
