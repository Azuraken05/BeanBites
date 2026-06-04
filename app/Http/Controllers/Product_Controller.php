<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Product_Controller extends Controller
{
    // Show all products
    public function index()
     {
        $products = Product::all(); // Get all products from database
        
        return view('products.index', [
            'products' => $products
        ]);
    }

    // Show add product form
    public function create()
    {
        return view('products.create');
    }

    // Store product in database
    public function store(Request $request)
    {
        Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'stock' => $request->stock,
            'user_id' => auth()->id(), // Current logged-in user
        ]);

        return redirect()->route('products.index')->with('success', 'Product added!');
    }
}
