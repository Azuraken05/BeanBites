<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Process User Registration Form Submission
    public function register(Request $request)
    {
        // 1. Core input rules verification (matching toast frontend parameters range 6-15)
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|max:15|confirmed',
        ]);

        // 2. Automatically capitalize the very first character of the submitted string handle
        $capitalizedUsername = ucfirst($request->username);

        // 3. Case-Insensitive Check: Prevent duplicate registration variations (e.g., avoiding conflicting 'azura' if 'Azura' exists)
        if (User::where('username', $capitalizedUsername)->exists()) {
            return response()->json([
                'success' => false, 
                'errors' => ['username' => ['This username has already been taken.']]
            ], 422);
        }

        // 4. Create user with securely encrypted password storage hashing rules
        User::create([
            'name' => $request->name,
            'username' => $capitalizedUsername, // Saved with capital starting letter rule directly inside the database
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['success' => true, 'message' => 'Account created successfully!']);
    }

    // Process Terminal Authentication Login Credentials Verified Checking
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Since logins might be typed lowercase by habit, normalize the checker to match database casing values smoothly
        $credentials['username'] = ucfirst($credentials['username']);

        // Attempt authentication via built-in Laravel session guards
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'The provided credentials do not match our records.'], 401);
    }

    // Secure Session Termination
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}