<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function () {
    // Auth routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        
        // Dashboard Routes (Privé pour les vendeurs)
        Route::get('/me/products', [App\Http\Controllers\Api\V1\ProductController::class, 'myProducts']);
        
        // C.R.U.D pour le vendeur
        Route::apiResource('products', App\Http\Controllers\Api\V1\ProductController::class)->except(['index', 'show']);
        Route::apiResource('menu-items', App\Http\Controllers\Api\V1\MenuItemController::class)->except(['index', 'show']);
        
        // Commandes et Paiement
        Route::post('/checkout', [\App\Http\Controllers\Api\V1\OrderController::class, 'checkout']);
    });

    // Webhooks de paiement (pas d'auth requise)
    Route::post('/fedapay/callback', [\App\Http\Controllers\Api\V1\OrderController::class, 'fedapayWebhook']);
    
    // Marketplace Public (Pas besoin d'être connecté pour consulter le catalogue)
    Route::get('/categories', [App\Http\Controllers\Api\V1\CategoryController::class, 'index']);
    Route::get('/categories/{id}', [App\Http\Controllers\Api\V1\CategoryController::class, 'show']);
    
    Route::get('/products', [App\Http\Controllers\Api\V1\ProductController::class, 'index']);
    Route::get('/products/{id}', [App\Http\Controllers\Api\V1\ProductController::class, 'show']);
    
    Route::get('/menu-items', [App\Http\Controllers\Api\V1\MenuItemController::class, 'index']);
    Route::get('/menu-items/{id}', [App\Http\Controllers\Api\V1\MenuItemController::class, 'show']);
});
