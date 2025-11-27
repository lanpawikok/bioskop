<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika belum login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Jika sudah login tapi bukan admin
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home');
        }

        // Jika admin, lanjutkan request
        return $next($request);
    }
}
