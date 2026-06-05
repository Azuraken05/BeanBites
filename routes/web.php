<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
    
    Route::get('/logout', [AuthController::class, 'logout']);
});
