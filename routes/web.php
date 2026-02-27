<?php

use Illuminate\Support\Facades\Route;

// Serve Quasar SPA for all routes (except /api and static files)
Route::get('/{any?}', function ($any = null) {
    // If a path is provided and the file exists, serve it directly
    if ($any) {
        $filePath = public_path($any);

        if (file_exists($filePath) && is_file($filePath)) {
            return response()->file($filePath);
        }
    }

    // Otherwise, serve the SPA
    $indexPath = public_path('index.html');

    if (!file_exists($indexPath)) {
        return response()->json([
            'message' => 'Frontend não compilado. Execute "npm run build" na pasta resources/app'
        ], 503);
    }

    return file_get_contents($indexPath);
})->where('any', '.*');
