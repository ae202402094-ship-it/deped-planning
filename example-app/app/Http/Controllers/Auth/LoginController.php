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

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Get credentials and include the status check
        $credentials = $request->only('email', 'password');
        $credentials['status'] = 1; // User must be approved/active

        // 2. Attempt login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect based on role
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect()->intended('/');
        }

        // 3. If login fails, check if it's because the account is just pending
        $userExists = \App\Models\User::where('email', $request->email)->first();
        if ($userExists && $userExists->status == 0) {
            return back()->withErrors([
                'email' => 'Your account is pending admin approval.',
            ]);
        }

        // 4. Otherwise, it's just a wrong password/email
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}