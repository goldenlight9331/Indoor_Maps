<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOrSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->role === 'Admin')) {
            return $next($request);
        }

        return redirect('/')->with('message', 'Access denied. You do not have the required permissions.');
    }
}
