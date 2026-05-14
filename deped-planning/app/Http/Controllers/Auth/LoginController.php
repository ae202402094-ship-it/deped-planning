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
        // 1. Validate the login input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Attempt to log the user in
        if (Auth::attempt($credentials)) {
            
            $user = Auth::user();

            // 3. NEW STATUS CHECK: Block Inactive accounts
            if ($user->status === 'inactive') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'email' => 'Your account is currently inactive. Please contact the Super Admin.',
                ]);
            }

            // 4. Regenerate session for security
            $request->session()->regenerate();

            // 5. Redirect based on their exact role
            if ($user->role === 'super_admin') {
                return redirect()->route('superadmin.dashboard');
            }

            // Regular admins go to the schools management page
            return redirect()->route('admin.schools');
        }

        // 6. If Auth::attempt fails
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request) 
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'You have been successfully logged out.');
    }
}