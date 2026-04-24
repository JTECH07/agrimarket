<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\V1\OrderController;

// ========================================
//   PUBLIC ROUTES
// ========================================
Route::get('/', [WebController::class, 'index'])->name('home');
Route::get('/catalog', [WebController::class, 'catalog'])->name('catalog');
Route::get('/catalog/{type}/{id}', [WebController::class, 'show'])->name('item.show');
Route::get('/checkout', function () { return view('checkout'); })->name('checkout');
Route::post('/checkout', [OrderController::class, 'checkout'])->middleware('auth')->name('checkout.process');

// ========================================
//   AUTH ROUTES
// ========================================
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [WebAuthController::class, 'register']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [WebAuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [WebAuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [WebAuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [WebAuthController::class, 'resetPassword'])->name('password.update');

// ========================================
//   DASHBOARD ROUTES (auth required)
// ========================================
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {

    // General
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::patch('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');

    // Products / Catalogue (Sellers only)
    Route::get('/products', [DashboardController::class, 'products'])->name('products');
    Route::get('/products/create', [DashboardController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [DashboardController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{id}/edit', [DashboardController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{id}', [DashboardController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [DashboardController::class, 'destroyProduct'])->name('products.destroy');

    // Orders
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [DashboardController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/confirm', [DashboardController::class, 'confirmOrder'])->name('orders.confirm');
    Route::patch('/orders/{id}/status', [DashboardController::class, 'updateOrderStatus'])->name('orders.update');

    // Deliveries
    Route::post('/deliveries/{id}/pickup', [DashboardController::class, 'pickupDelivery'])->name('deliveries.pickup');
    Route::post('/deliveries/{id}/complete', [DashboardController::class, 'completeDelivery'])->name('deliveries.complete');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [DashboardController::class, 'adminUsers'])->name('users');
        Route::post('/users/{id}/verify', [DashboardController::class, 'verifyUser'])->name('users.verify');
        Route::delete('/users/{id}', [DashboardController::class, 'deleteUser'])->name('users.delete');
    });
});
