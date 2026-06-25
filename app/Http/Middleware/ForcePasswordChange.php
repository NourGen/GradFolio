<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forces users to change their password if must_change_password = true.
 * Applied to all authenticated graduate routes.
 */
class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            // Allow the change-password route and logout
            if (! $request->routeIs('password.change', 'password.change.update', 'logout')) {
                return redirect()->route('password.change')
                    ->with('error', '⚠️ Please set a new password before continuing.');
            }
        }

        return $next($request);
    }
}
