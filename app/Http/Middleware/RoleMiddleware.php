<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role-Based Access Control Middleware
 *
 * Usage: Route::middleware('role:admin') or Route::middleware('role:graduate')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Admin has access to everything
        if ($user->isAdmin()) {
            // But redirect admins away from graduate-only routes
            if ($role === 'graduate') {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Admin accounts use the Admin Panel.');
            }
            return $next($request);
        }

        // Graduate role check
        if ($role === 'graduate' && $user->isGraduate()) {
            return $next($request);
        }

        // Admin-only route, but user is not admin
        if ($role === 'admin') {
            abort(403, 'Access restricted to administrators only.');
        }

        abort(403, 'Unauthorized.');
    }
}
