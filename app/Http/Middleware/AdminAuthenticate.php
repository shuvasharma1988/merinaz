<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. If not logged in at all, go to admin login
        if (! Auth::check()) {
            return redirect()->route('admin.login');
        }

        // 2. If logged in but NOT an admin (e.g., a standard customer), redirect to public home
        if (! Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', 'You do not have administrative access.');
        }

        // 3. Authenticated and has admin rights
        return $next($request);
    }
}
