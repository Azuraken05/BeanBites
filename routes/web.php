<?php

use Illuminate\Support\Facades\Route;

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