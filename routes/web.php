<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

<<<<<<< HEAD
    // 1. This displays your custom login page on a GET request
    Route::get('/', function () {
        return view('login');
    });

    // 2. NEW: This intercepts the form submit POST request and pushes it to the dashboard
    Route::post('/', function () {
        return redirect('/dashboard');
    });

    // 3. This displays your sign-up registration module
    Route::get('/register', function () {
        return view('register');
    });

    // 4. This displays your master dynamic Dashboard module template layout
    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    Route::get('/products', function () {
        return view('products');
    });

    Route::get('/pos', function () {
        return view('pos');
    });

    Route::get('/reports', function () {
        return view('reports');
    });
=======
// Guest Protection Group (Accessible only when logged out)
Route::middleware('guest')->group(function () {
    Route::get('/', function () { return view('login'); })->name('login');
    Route::get('/register', function () { return view('register'); });
    
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Auth Security Guard Group (Accessible only when legally logged in)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () { return view('dashboard'); });
    Route::get('/products', function () { return view('products'); });
    Route::get('/pos', function () { return view('pos'); });
    Route::get('/reports', function () { return view('reports'); });
    
    Route::get('/logout', [AuthController::class, 'logout']);
});
>>>>>>> 57661bda363f3ec5b0b741bdea17b76797df1f9d
