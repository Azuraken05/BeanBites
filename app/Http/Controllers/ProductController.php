<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // Render view page showing ALL registered products to every user
    public function index()
    {
        // Fetches non-deleted products dynamically across active data pipelines
        $products = Product::orderBy('created_at', 'desc')->get();
        return view('products', compact('products'));
    }

    // Securely handle incoming form uploads via AJAX requests
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Saves images cleanly inside storage/app/public/products
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Save data and tie it to the currently logged-in user session
        $product = Product::create([
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'stock' => $request->stock, // Saves as null automatically if turned off
            'image_path' => $imagePath,
            'user_id' => Auth::id(), 
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product saved successfully!',
            'product' => $product
        ]);
    }

    // Fetch a single product's data payload for model hydration inside the edit window
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    // Update active product properties inside the database records via AJAX
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $product = Product::findOrFail($id);

        // Retain original image string mapping pointer if a new file payload is omitted
        $imagePath = $product->image_path;
        
        if ($request->hasFile('image')) {
            // Clean up old obsolete thumbnail source from disk space if a replacement is set
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            // Fallback column map parameters to null values if tracking toggles are turned off
            'stock' => $request->track_stock == '1' ? $request->stock : null,
            'image_path' => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully!',
            'product' => $product
        ]);
    }

    // Fetch all active products grouped together for the POS terminal view interface
    public function posIndex()
    {
        $products = Product::orderBy('name', 'asc')->get();
        return view('pos', compact('products'));
    }

    // NEW: Secure transaction processor logic to handle checkout arrays from POS terminal
    public function checkout(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'total_amount' => 'required|numeric',
            'total_items' => 'required|integer'
        ]);

        // Use a database transaction to ensure data integrity and absolute safety during updates
        DB::beginTransaction();
        try {
            $order = Order::create([
                'total_amount' => $request->total_amount,
                'total_items' => $request->total_items,
                'user_id' => Auth::id() // Tracks logged-in cashier profile instance ID
            ]);

            foreach ($request->cart as $productId => $cartItem) {
                // Apply a pessimistic database row lock preventing checkout concurrency race conditions
                $product = Product::lockForUpdate()->findOrFail($productId);

                // Double check stock boundaries safeguards on the server-side right before execution saves
                if ($product->stock !== null) {
                    if ($product->stock < $cartItem['quantity']) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Could not complete transaction. {$product->name} only has {$product->stock} items remaining."
                        ], 422);
                    }
                    
                    // Deduct stock inventories levels instantly inside the database row table rows
                    $product->decrement('stock', $cartItem['quantity']);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $cartItem['quantity'],
                    'price' => $product->price
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Order processed successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Database error encountered during structural save pipelines.'
            ], 500);
        }
    }

    // Log contextual deletion remarks and hide row out of active sales streams
    public function destroy(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string|max:500' // Enforce validation constraint mechanics on reasons input fields
        ]);

        $product = Product::findOrFail($id);

        // Track data parameters to the row entry before running soft deletion macros
        $product->update([
            'delete_remarks' => $request->remarks,
            'deleted_by_user_id' => Auth::id()
        ]);

        // Triggers the soft delete trait mechanism cleanly inside the database tables
        $product->delete(); 

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    }
}