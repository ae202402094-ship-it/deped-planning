<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    protected function authenticated(Request $request, $user)
    {
        if (!$user->isApproved()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your email is not verified or your account is not yet approved.');
        }

        // Redirect based on role
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        return redirect()->route('admin.schools');
    }

    public function login(Request $request)
{
    // Validate the login input
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // 1. Attempt to log the user in
    if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        
        $user = \Illuminate\Support\Facades\Auth::user();

        // 2. Check if they are approved by the Super Admin
        if (!$user->isApproved()) { // Uses the helper we made in User.php
            \Illuminate\Support\Facades\Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return back()->withErrors([
                'email' => 'Your account is pending approval by the Super Admin.',
            ]);
        }

        // 3. User is approved, regenerate session for security
        $request->session()->regenerate();

        // 4. Redirect based on their exact role
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }

        // Regular admins go to the schools management page
        return redirect()->route('admin.schools');
    }

    // 5. If Auth::attempt fails (wrong password or email)
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
}

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'You have been successfully logged out.');
    }
}