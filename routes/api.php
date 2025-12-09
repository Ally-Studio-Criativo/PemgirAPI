<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\ColorPaletteController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Admin\CampaignController as AdminCampaignController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public Routes (Authentication)
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Public Routes (Read-only for frontend)
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    Route::get('palettes', [ColorPaletteController::class, 'index']);
    Route::get('palettes/{palette}', [ColorPaletteController::class, 'show']);
    Route::get('colors', [ColorController::class, 'index']);
    Route::get('colors/{color}', [ColorController::class, 'show']);
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    Route::get('campaign/active', [CampaignController::class, 'active']);

    // Protected Routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {

        // Auth Routes
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        // Users Management
        Route::apiResource('users', UserController::class);

        // Categories Management
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{category}', [CategoryController::class, 'update']);
        Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

        // Color Palettes Management
        Route::post('palettes', [ColorPaletteController::class, 'store']);
        Route::put('palettes/{palette}', [ColorPaletteController::class, 'update']);
        Route::delete('palettes/{palette}', [ColorPaletteController::class, 'destroy']);

        // Colors Management
        Route::post('colors', [ColorController::class, 'store']);
        Route::put('colors/{color}', [ColorController::class, 'update']);
        Route::delete('colors/{color}', [ColorController::class, 'destroy']);

        // Products Management
        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{product}', [ProductController::class, 'update']);
        Route::delete('products/{product}', [ProductController::class, 'destroy']);

        // Product Images Management
        Route::get('products/{product}/images', [ProductImageController::class, 'index']);
        Route::post('products/{product}/images', [ProductImageController::class, 'store']);
        Route::put('products/{product}/images/{image}', [ProductImageController::class, 'update']);
        Route::post('products/{product}/images/reorder', [ProductImageController::class, 'reorder']);
        Route::delete('products/{product}/images/{image}', [ProductImageController::class, 'destroy']);

        // Images Management
        Route::apiResource('images', ImageController::class);

        // Campaigns Management
        Route::get('campaigns', [AdminCampaignController::class, 'index']);
        Route::post('campaigns', [AdminCampaignController::class, 'store']);
        Route::get('campaigns/{campaign}', [AdminCampaignController::class, 'show']);
        Route::put('campaigns/{campaign}', [AdminCampaignController::class, 'update']);
        Route::delete('campaigns/{campaign}', [AdminCampaignController::class, 'destroy']);
    });

});
