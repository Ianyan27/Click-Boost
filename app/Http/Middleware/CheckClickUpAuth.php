<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckClickUpAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('authenticated')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        return $next($request);
    }
}