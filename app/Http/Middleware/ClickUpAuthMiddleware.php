<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ClickUpAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Session::get('authenticated')) {
            return redirect('/login')->withErrors(['error' => 'Please login to continue.']);
        }

        return $next($request);
    }
}