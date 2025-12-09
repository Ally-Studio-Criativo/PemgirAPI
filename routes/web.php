<?php

use Illuminate\Support\Facades\Route;

// Serve Quasar SPA for all routes (except /api)
Route::get('/{any}', function () {
    $indexPath = public_path('index.html');

    if (!file_exists($indexPath)) {
        return response()->json([
            'message' => 'Frontend não compilado. Execute "npm run build" na pasta resources/app'
        ], 503);
    }

    return file_get_contents($indexPath);
})->where('any', '.*');
