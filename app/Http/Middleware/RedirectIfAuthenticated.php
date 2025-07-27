<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
    *
    * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
    */

    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

    foreach ($guards as $guard) {
        if (Auth::guard($guard)->check()) {
        // Perbaikan: Redirect berdasarkan role jika user sudah login
        $user = Auth::guard($guard)->user();
        if ($user->role === 'manager' || $user->role === 'admin') {
        return redirect()->route('dashboard');
        } elseif ($user->role === 'karyawan') {
        return redirect()->route('absensi.dashboard');
        }
        return redirect('/dashboard'); // Fallback
        }
    }

        return $next($request);
    }
}
