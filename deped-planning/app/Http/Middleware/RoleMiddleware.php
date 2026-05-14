<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
{
    if (!Auth::check()) {
        return redirect('/login');
    }

    // Block Inactive users and flush their session
    if (Auth::user()->status === 'inactive') {
        Auth::logout();
        
        // Add these two lines to ensure the "Inactive" ghost is gone
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('error', 'Your account is currently inactive. Please contact the Super Admin.');
    }

    if (!in_array(Auth::user()->role, $roles)) {
        abort(403, 'UNAUTHORIZED ACCESS.');
    }

    return $next($request);
}
}