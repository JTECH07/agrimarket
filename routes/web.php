<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WebController;

use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\DashboardController;

Route::get('/', [WebController::class, 'index']);
Route::get('/catalog', [WebController::class, 'catalog']);
Route::get('/catalog/{type}/{id}', [WebController::class, 'show'])->name('item.show');
Route::get('/checkout', function() { return view('checkout'); })->name('checkout');

// Auth Routes
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [WebAuthController::class, 'register']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Dashboard Routes (Sellers only)
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/products', [DashboardController::class, 'products'])->name('products');
    Route::get('/products/create', [DashboardController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [DashboardController::class, 'storeProduct'])->name('products.store');
    Route::get('/products/{id}/edit', [DashboardController::class, 'editProduct'])->name('products.edit');
    Route::put('/products/{id}', [DashboardController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [DashboardController::class, 'destroyProduct'])->name('products.destroy');
    Route::get('/orders', [DashboardController::class, 'orders'])->name('orders');
    Route::post('/orders/{id}/confirm', [DashboardController::class, 'confirmOrder'])->name('orders.confirm');
    Route::patch('/orders/{id}/status', [DashboardController::class, 'updateOrderStatus'])->name('orders.update');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::patch('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
});
