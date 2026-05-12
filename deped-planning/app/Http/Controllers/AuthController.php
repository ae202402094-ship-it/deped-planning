<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() { 
        return view('auth.login'); 
    }

public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // Check if user is an admin and if they are approved
        if ($user->role === 'admin' && $user->status !== 'approved') {
            Auth::logout();
            return back()->withErrors(['email' => 'Your admin account is pending approval by a Superadmin.']);
        }

        return redirect()->intended('/admin/dashboard');
    }

    return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
}

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'You have been successfully logged out.');
    }
}