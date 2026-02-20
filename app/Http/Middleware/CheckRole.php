<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if user has the required role
        $user = Auth::user();
        
        if ($user->role !== $role) {
            // Redirect to appropriate dashboard based on user's actual role
            if ($user->role === 'kasi') {
                return redirect()->route('kasi.dashboard')->with('error', 'Akses ditolak');
            } elseif ($user->role === 'kasun') {
                return redirect()->route('kasun.dashboard')->with('error', 'Akses ditolak');
            }
            
            // If no valid role, logout
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akses tidak valid');
        }

        return $next($request);
    }
}
