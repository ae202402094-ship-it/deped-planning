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

    public function showRegister() { 
        return view('auth.register'); 
    }

    public function register(Request $request) {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'admin',     // Default role for registration
            'status'   => 'pending',  // Matches your migration
        ]);

        // This line is what sends the email!
        event(new Registered($user));

        // Log the user in so they can see the "Verify your email" page
        Auth::login($user);

        return redirect()->route('verification.notice')
                        ->with('success', 'Registration successful! Please check your email for a verification link.');
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