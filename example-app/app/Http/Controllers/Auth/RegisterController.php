<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function showRegistrationForm() 
    {
        return view('auth.register');
    }

    public function register(Request $request) 
    {
        // 1. Validate Input
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        // 2. Create User (Stored as Pending Approval)
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 0, // 0 = Pending Admin Approval
        ]);

        // 3. Trigger Email Verification Event
        // This fires the CustomVerifyEmail notification we set up earlier
        event(new Registered($user));

        // 4. Log the user in
        // This is crucial so they can access the 'verification.notice' 
        // and 'verification.send' (resend) routes
        Auth::login($user);

        // 5. Redirect to the Verification Instructions page
        return redirect()->route('verification.notice');
    }
}