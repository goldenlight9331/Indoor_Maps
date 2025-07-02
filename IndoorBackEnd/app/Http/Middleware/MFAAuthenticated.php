<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class MFAAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if user is logged in and MFA is not verified
        if (Auth::check() && !session()->has('mfa_verified')) {
            return redirect()->route('mfa.verify'); // Redirect to MFA input page
        }

        return $next($request);
    }
}