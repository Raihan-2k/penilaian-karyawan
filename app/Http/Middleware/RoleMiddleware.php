<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized (Not Authenticated).');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            dd([
                'Current URL' => $request->fullUrl(),
                'User ID' => $user->id,
                'User Name' => $user->name,
                'User Role (from Auth::user())' => $user->role,
                'Allowed Roles (from route middleware)' => $roles,
                'Is Role Allowed?' => in_array($user->role, $roles),
            ]);
            abort(403, 'Unauthorized (Role not allowed).');
        }

        return $next($request);
    }
}

