<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\DashboardController;

Route::get('/register', function () {
    return view('register');
});

Route::post('/register', function () {
    return redirect('/');
});
// Auth Security Guard Group (Accessible only when legally logged in)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () { return view('dashboard'); });
    Route::get('/products', function () { return view('products'); });
    Route::get('/pos', function () { return view('pos'); });
    Route::get('/reports', function () { return view('reports'); });
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');
    Route::post('/products/delete/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/logout', [AuthController::class, 'logout']);
<<<<<<< HEAD
});
=======
    Route::get('/products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('/products/update/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::get('/pos', [ProductController::class, 'posIndex'])->name('pos.index');
    Route::post('/pos/checkout', [ProductController::class, 'checkout'])->name('pos.checkout');
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});
>>>>>>> c8ef1ff2d23043ba8a6344a5f1f3b294556b49b8
