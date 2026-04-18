<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WebController;

Route::get('/', [WebController::class, 'index']);
Route::get('/catalog', [WebController::class, 'catalog']);
Route::get('/checkout', function() { return view('checkout'); });
