@extends('layouts.public')

@section('content')
<div class="login-container" style="display: flex; align-items: center; justify-content: center; min-height: 80vh; background-color: #f3f4f6;">
    <div class="login-card" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px;">
        
        <!-- DepED Branding -->
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #b91c1c;">DepED Planning System</h2>
            <p style="color: #6b7280; font-size: 0.875rem;">Sign in with your IT-provided account</p>
        </div>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <!-- Email Input -->
            <div style="margin-bottom: 1rem;">
                <label for="email" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Email Address</label>
                <input type="email" name="email" id="email" required autofocus 
                    style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border-color 0.2s;"
                    onfocus="this.style.borderColor='#b91c1c'" onblur="this.style.borderColor='#d1d5db'">
                @error('email')
                    <span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password Input -->
            <div style="margin-bottom: 1rem;">
                <label for="password" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.25rem;">Password</label>
                <input type="password" name="password" id="password" required 
                    style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 6px; outline: none; transition: border-color 0.2s;"
                    onfocus="this.style.borderColor='#b91c1c'" onblur="this.style.borderColor='#d1d5db'">
            </div>

            <!-- Options -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; font-size: 0.875rem; color: #374151;">
                    <input type="checkbox" name="remember" style="margin-right: 0.5rem; border-radius: 4px;"> Remember me
                </label>
            </div>

            <!-- Login Button -->
            <button type="submit" style="width: 100%; padding: 0.75rem; background-color: #b91c1c; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: background-color 0.2s;"
                onmouseover="this.style.backgroundColor='#991b1b'" onmouseout="this.style.backgroundColor='#b91c1c'">
                Login
            </button>
        </form>

        <!-- Removed Register Link section entirely -->
        <div style="margin-top: 1.5rem; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 1rem;">
            <p style="font-size: 0.75rem; color: #9ca3af;">If you cannot access your account, please contact the IT department.</p>
        </div>
    </div>
</div>
@endsection