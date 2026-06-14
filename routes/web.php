<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\DashboardController;

// Guest Protection Group (Accessible only when logged out)
Route::middleware('guest')->group(function () {
    Route::get('/', function () { return view('login'); })->name('login');
    Route::get('/register', function () { return view('register'); });
    
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Auth Security Guard Group (Accessible only when legally logged in)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('/products/update/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::post('/products/delete/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/pos', [ProductController::class, 'posIndex'])->name('pos.index');
    Route::post('/pos/checkout', [ProductController::class, 'checkout'])->name('pos.checkout');
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/logout', [AuthController::class, 'logout']);
});