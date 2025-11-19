<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsManagement
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (! $request->user()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Check if user has 'management' role
        if ($request->user()->role !== 'management') {
            abort(403, 'Akses ditolak. Anda bukan manajemen.');
        }

        return $next($request);
    }
}
